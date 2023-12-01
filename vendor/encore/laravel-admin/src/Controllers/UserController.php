<?php

namespace Encore\Admin\Controllers;

use App\Admin\Controllers\ConservationAreaController;
use App\Models\ConservationArea;
use App\Models\Enterprise;
use App\Models\Location;
use App\Models\PA;
use App\Models\Utils;
use Dflydev\DotAccessData\Util;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Hash;

class UserController extends AdminController
{
    /**
     * {@inheritdoc}
     */
    protected function title()
    {
        return 'Users';
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {

        /*
district_id
address
middle_name

Edit Edit


        */
        $userModel = config('admin.database.users_model');
        $grid = new Grid(new $userModel());

        $grid->filter(function ($f) {
            $f->disableIdFilter();

            $district_ajax_url = url(
                '/api/ajax?'
                    . "&search_by_1=name"
                    . "&search_by_2=id"
                    . "&query_parent=0"
                    . "&model=Location"
            );
            $f->equal('district_id', 'Filter by district')->select(function ($id) {
                $a = Location::find($id);
                if ($a) {
                    return [$a->id => "#" . $a->id . " - " . $a->name];
                }
            })
                ->ajax($district_ajax_url);
        });

        $grid->quickSearch('name')->placeholder('Search by name...');
        $grid->disableBatchActions();
        $grid->model()->orderBy('id', 'Desc');
        $grid->column('id', 'ID')
            ->width(40)
            ->sortable();
        $grid->column('avatar', __('Photo'))
            ->width(70)
            ->lightbox(['width' => 60, 'height' => 80]);
        $grid->column('name', 'Name')->sortable();
        $grid->column('sex', 'Gender')->filter([
            'Male' => 'Male',
            'Female' => 'Female',
        ])->sortable();

        $grid->column('phone_number_1', 'Phone number');

        $grid->column('ca', 'Conservation area')->display(function () {
            return $this->ca->name;
        })->sortable();

        $grid->pa()->name('Duty station')->sortable();
        
        $grid->column('phone_number_2', 'Phone number 2')->hide();
        $grid->column('date_of_birth', 'D.O.B')->display(function ($f) {
            return Utils::my_date($f);
        });

        /* 
        $grid->column('district_id', 'District')->display(function ($id) {
            return Utils::get(Location::class, $id)->name_text;
        })->sortable();
        $grid->column('sub_county_id', 'Sub county')->display(function ($id) {
            return Utils::get(Location::class, $id)->name_text;
        })->sortable();
 */


        $grid->column('email', 'email address');
        $grid->column('username', 'username');
        $grid->column('roles', trans('admin.roles'))->pluck('name')->label();
        $grid->column('created_at', 'Registered')->display(function ($f) {
            return Utils::my_date($f);
        });


        $grid->actions(function (Grid\Displayers\Actions $actions) {
            if ($actions->getKey() == 1) {
                $actions->disableDelete();
            }
        });

        $grid->tools(function (Grid\Tools $tools) {
            $tools->batch(function (Grid\Tools\BatchActions $actions) {
                $actions->disableDelete();
            });
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        $userModel = config('admin.database.users_model');

        $show = new Show($userModel::findOrFail($id));

        $show->field('id', 'ID');
        $show->field('username', trans('admin.username'));
        $show->field('name', trans('admin.name'));
        $show->field('roles', trans('admin.roles'))->as(function ($roles) {
            return $roles->pluck('name');
        })->label();
        $show->field('permissions', trans('admin.permissions'))->as(function ($permission) {
            return $permission->pluck('name');
        })->label();
        $show->field('created_at', trans('admin.created_at'));
        $show->field('updated_at', trans('admin.updated_at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        $userModel = config('admin.database.users_model');
        $permissionModel = config('admin.database.permissions_model');
        $roleModel = config('admin.database.roles_model');

        $form = new Form(new $userModel());
        $form->disableReset();


        $userTable = config('admin.database.users_table');
        $connection = config('admin.database.connection');
        $form->display('id', 'ID');


        $form->text('first_name', 'First name')->rules('required');
        $form->text('middle_name', 'Middle name');
        $form->text('last_name', 'Last name')->rules('required');
        $form->date('date_of_birth', 'Date of birth');


        $form->radio('sex', __('Gender'))->options([
            'Male' => 'Male',
            'Female' => 'Female',
        ])->rules('required');

        $form->text('phone_number_1', 'Phone number')->rules('required');
        $form->text('phone_number_2', 'Phone number 2');

        $form->select('pa_id', __('Duty station'))
            ->rules('required')
            ->help('Conservation area where  user is assigned')
            ->options(PA::pluck('name', 'id'));

 

        $form->text('address', 'UWA staff number');
        $form->divider();

        $form->email('email', 'Email address')
            ->creationRules(['required', "unique:{$connection}.{$userTable}"])
            ->updateRules(['required', "unique:{$connection}.{$userTable},email,{{id}}"]);

        $form->hidden('enterprise_id')->value(1)->default(1);


        $form->image('avatar', 'Profile photo');
        $form->password('password', trans('admin.password'))->rules('required|confirmed');
        $form->password('password_confirmation', trans('admin.password_confirmation'))->rules('required')
            ->default(function ($form) {
                return $form->model()->password;
            });

        $form->ignore(['password_confirmation']);

        $form->multipleSelect('roles', trans('admin.roles'))->options($roleModel::all()->pluck('name', 'id'));
        //$form->multipleSelect('permissions', trans('admin.permissions'))->options($permissionModel::all()->pluck('name', 'id'));

        $form->display('created_at', trans('admin.created_at'));
        $form->display('updated_at', trans('admin.updated_at'));

        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = Hash::make($form->password);
            }
        });

        return $form;
    }
}
