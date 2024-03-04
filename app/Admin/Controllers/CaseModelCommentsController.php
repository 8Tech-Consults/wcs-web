<?php

namespace App\Admin\Controllers;

use App\Models\CaseModel;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CaseModelCommentsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Case progress comments';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        Admin::script('window.location.replace("' . admin_url('cases') . '");');
        return 'Loading...';
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
        $grid->column('conservation_area_id', __('Conservation area id'));
        $grid->column('offense_category', __('Offense category'));
        $grid->column('case_submitted', __('Case submitted'));
        $grid->column('case_step', __('Case step'));
        $grid->column('add_more_suspects', __('Add more suspects'));
        $grid->column('case_date', __('Case date'));
        $grid->column('officer_in_charge', __('Complainant'));
        $grid->column('court_file_status', __('Court file status'));
        $grid->column('prison', __('Prison'));
        $grid->column('jail_release_date', __('Jail release date'));
        $grid->column('suspect_appealed', __('Accused appealed'));
        $grid->column('suspect_appealed_date', __('Accused appealed date'));
        $grid->column('suspect_appealed_court_name', __('Accused appealed court name'));
        $grid->column('suspect_appealed_court_file', __('Accused appealed court file'));
        $grid->column('user_adding_suspect_id', __('User adding suspect id'));

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
        $show->field('conservation_area_id', __('Conservation area id'));
        $show->field('offense_category', __('Offense category'));
        $show->field('case_submitted', __('Case submitted'));
        $show->field('case_step', __('Case step'));
        $show->field('add_more_suspects', __('Add more suspects'));
        $show->field('case_date', __('Case date'));
        $show->field('officer_in_charge', __('Complainant'));
        $show->field('court_file_status', __('Court file status'));
        $show->field('prison', __('Prison'));
        $show->field('jail_release_date', __('Jail release date'));
        $show->field('suspect_appealed', __('Accused appealed'));
        $show->field('suspect_appealed_date', __('Accused appealed date'));
        $show->field('suspect_appealed_court_name', __('Accused appealed court name'));
        $show->field('suspect_appealed_court_file', __('Accused appealed court file'));
        $show->field('user_adding_suspect_id', __('User adding suspect id'));

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


        $arr = (explode('/', $_SERVER['REQUEST_URI']));
        $case = null;
        $case = CaseModel::find($arr[2]);
        if ($case != null) {
        } else {
            die("Exhibit not found.");
        }

        $form->display('Case')->default($case->case_number);

        $form->morphMany('comments', 'Click on new to add a case progress comment', function (Form\NestedForm $form) {
            $u = Admin::user();
            $form->hidden('comment_by')->default($u->id);
            $form->text('body', __('Progress comment'))->rules('required');
        });

        $form->tools(function ($actions) {
            $actions->disableDelete();
            $actions->disableView();
            $actions->disableView();
            $actions->disableList();
        });

        $form->disableCreatingCheck();
        $form->disableEditingCheck();
        $form->disableReset();
        $form->disableViewCheck();

        return $form;
    }
}
