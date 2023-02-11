<?php

namespace App\Admin\Controllers;

use App\Models\CaseModel;
use App\Models\Utils;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Auth;

class NewExhibitsCaseModelController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Creating new case - exhibits';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {



        $pendingCase = Utils::hasPendingCase(Auth::user());
        if ($pendingCase != null) {
            if ($pendingCase->case_step == 1) {
                return redirect(admin_url('new-case-suspects/create'));
            } else if ($pendingCase->case_step == 2) {
                return redirect(admin_url("new-exhibits-case-models/{$pendingCase->id}/edit"));
            } else if ($pendingCase->case_step == 3) {
                return redirect(admin_url("new-confirm-case-models/{$pendingCase->id}/edit"));
            } else {
            }
            //dd($pendingCase);
        }


        $grid = new Grid(new CaseModel());

        $grid->column('id', __('Id'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        $grid->column('reported_by', __('Reported by'));
        $grid->column('latitude', __('Latitude'));
        $grid->column('longitude', __('Longitude'));
        $grid->column('district_id', __('District id'));
        $grid->column('sub_county_id', __('Sub county id'));
        $grid->column('parish', __('Parish'));
        $grid->column('village', __('Village'));
        $grid->column('offence_category_id', __('Offence category id'));
        $grid->column('offence_description', __('Offence description'));
        $grid->column('is_offence_committed_in_pa', __('Is offence committed in pa'));
        $grid->column('pa_id', __('Pa id'));
        $grid->column('has_exhibits', __('Has exhibits'));
        $grid->column('status', __('Status'));
        $grid->column('title', __('Title'));
        $grid->column('location_picker', __('Location picker'));
        $grid->column('deleted_at', __('Deleted at'));
        $grid->column('case_number', __('Case number'));
        $grid->column('done_adding_suspects', __('Done adding suspects'));
        $grid->column('ca_id', __('Ca id'));
        $grid->column('detection_method', __('Detection method'));
        $grid->column('ca_id', __('Conservation area id'));
        $grid->column('offense_category', __('Offense category'));
        $grid->column('case_submitted', __('Case submitted'));
        $grid->column('case_step', __('Case step'));
        $grid->column('add_more_suspects', __('Add more suspects'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(CaseModel::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('reported_by', __('Reported by'));
        $show->field('latitude', __('Latitude'));
        $show->field('longitude', __('Longitude'));
        $show->field('district_id', __('District id'));
        $show->field('sub_county_id', __('Sub county id'));
        $show->field('parish', __('Parish'));
        $show->field('village', __('Village'));
        $show->field('offence_category_id', __('Offence category id'));
        $show->field('offence_description', __('Offence description'));
        $show->field('is_offence_committed_in_pa', __('Is offence committed in pa'));
        $show->field('pa_id', __('Pa id'));
        $show->field('has_exhibits', __('Has exhibits'));
        $show->field('status', __('Status'));
        $show->field('title', __('Title'));
        $show->field('location_picker', __('Location picker'));
        $show->field('deleted_at', __('Deleted at'));
        $show->field('case_number', __('Case number'));
        $show->field('done_adding_suspects', __('Done adding suspects'));
        $show->field('ca_id', __('Ca id'));
        $show->field('detection_method', __('Detection method'));
        $show->field('ca_id', __('Conservation area id'));
        $show->field('offense_category', __('Offense category'));
        $show->field('case_submitted', __('Case submitted'));
        $show->field('case_step', __('Case step'));
        $show->field('add_more_suspects', __('Add more suspects'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {


        $form = new Form(new CaseModel());

        $pendingCase = Utils::hasPendingCase(Auth::user());
        if ($pendingCase == null) {
            die("active case not found.");
        } else {
            $form->hidden('case_id', 'Suspect photo')->default($pendingCase->id)->value($pendingCase->id);
        }


        $form = new Form(new CaseModel());
        Admin::css(url('/css/new-case.css'));

        $form->disableCreatingCheck();
        $form->disableReset();
        $form->disableEditingCheck();
        $form->disableViewCheck();
        $form->html(view('steps', ['active' => 3, 'case' => $pendingCase]));


        $form->html('<a class="btn btn-danger" href="' . admin_url("new-confirm-case-models/{$pendingCase->id}/edit") . '" >SKIP TO SUBMIT</a>', 'SKIP');


        $form->morphMany('exhibits', 'Click on new to add exhibit', function (Form\NestedForm $form) {

            $form->select('exhibit_catgory', __('Exhibit category'))
                ->options([
                    'Wildlife' => 'Wildlife',
                    'Implements' => 'Implements',
                    'Implement & Wildlife' => 'Both Implement & Wildlife',
                ])
                ->rules('required');
            $form->text('wildlife', __('Species'));
            $form->decimal('quantity', __('Quantity (in KGs)'))
                ->rules('required');

            $form->text('implement', __('Implements'));
            $form->textarea('description', __('Description'))
                ->rules('required');
            /* $form->textarea('wildlife', __('Wildlife'));
            $form->textarea('implements', __('Implements')); */

            $form->image('photos', __('Exhibit Photo'));
        });
        return $form;
    }
}
