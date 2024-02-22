<?php

namespace App\Admin\Controllers;

use App\Models\Animal;
use App\Models\CaseModel;
use App\Models\Exhibit;
use App\Models\ImplementType;
use App\Models\Specimen;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Auth;

class AddExhibitCaseModelController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Adding new exhibit to case';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {

        Admin::script('window.location.replace("' . admin_url("cases") . '");');
        return 'Loading...';
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {

        Admin::script('window.location.replace("' . admin_url("cases") . '");');
        return 'Loading...';

        $show = new Show(Exhibit::findOrFail($id));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {


        //fet id from segment 2
        $id = (int)request()->segment(2);
        $ex = Exhibit::find($id);


        if ($ex == null) {
            $arr = (explode('/', $_SERVER['REQUEST_URI']));
            $pendingCase = null;
            $ex = Exhibit::find($arr[2]);
            if ($ex != null) {
                $pendingCase = CaseModel::find(session('pending_case_id'));
            } else {
                die("Exhibit not found.");
            }
            if ($pendingCase == null) {
                die("Case not found.");
            }
        } else {
            $pendingCase = $ex;
        }


        $form = new Form(new Exhibit());
        $form->disableEditingCheck();

        $form->hidden('case_id')->default(0);
        $form->display('ADDING EXHIBIT TO CASE')->default($pendingCase->case->case_number);
        $form->divider();

        $form->disableCreatingCheck();
        $form->disableReset();
        $form->disableViewCheck();

        $form->radio('type_wildlife', __('Exibit type Wildlife?'))
            ->options([
                'Yes' => 'Yes',
                'No' => 'No',
            ])
            ->when('Yes', function ($form) {
                $form->divider('Wildlife Exibit(s) Information');


                $options =  Animal::where([])->orderBy('id', 'desc')->get()->pluck('name', 'id');
                $form->select('wildlife_species', 'Select Species')->options($options)
                    ->when(1, function ($form) {
                        $form->text('other_wildlife_species', 'Specify Species')
                            ->rules('required');
                    })
                    ->rules('required');


                $form->select('specimen', 'Specimen')->options(
                    Specimen::all()->pluck('name', 'name')
                )->rules('required');

                $form->decimal('wildlife_quantity', __('Quantity (in KGs)'));
                $form->decimal('wildlife_pieces', __('Number of pieces'));
                $form->text('wildlife_description', __('Description'));
                $form->multipleFile('wildlife_attachments', __('Wildlife exhibit(s) attachments files or photos'));
                $form->divider();
            });
        $form->radio('type_implement', __('Exibit type Implement?'))
            ->options([
                'Yes' => 'Yes',
                'No' => 'No',
            ])
            ->when('Yes', function ($form) {
                $form->divider('Implements Exibit(s) Information')->rules('required');

                $options =  ImplementType::where([])->orderBy('id', 'desc')->get()->pluck('name', 'id');
                $form->select('implement_name', 'Select implement')->options($options)
                    ->when(1, function ($form) {
                        $form->text('other_implement', 'Specify implement')
                            ->rules('required');
                    })
                    ->rules('required');

                $form->decimal('implement_pieces', __('No of pieces'));
                $form->text('implement_description', __('Description'));
                $form->multipleFile('implement_attachments', __('Implements exhibit(s) attachments files or photos'));
                $form->divider();
            });

        $form->radio('type_other', __('Other exhibit types?'))
            ->options([
                'Yes' => 'Yes',
                'No' => 'No',
            ])
            ->when('Yes', function ($form) {
                $form->divider('Other Exibit(s) Information')->rules('required');
                $form->text('others_description', __('Description for others'));
                $form->multipleFile('others_attachments', __('Attachments'));
                $form->divider();
            });

        $form->saving(function (Form $form) {
            $form->case_id = session('pending_case_id');
            session()->forget('pending_case_id');
        });

        $form->saved(function (Form $form) {
            if ($form->type_other == 'No' && $form->type_implement == 'No' && $form->type_wildlife == 'No') {
                Exhibit::find($form->model()->id)->delete(); //remove the unnecessary exhibit
            }
            return redirect(admin_url("cases"));
        });

        return $form;
    }
}
