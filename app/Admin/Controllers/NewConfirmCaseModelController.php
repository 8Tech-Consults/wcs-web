<?php

namespace App\Admin\Controllers;

use App\Models\CaseModel;
use App\Models\CaseSuspect;
use App\Models\Utils;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Auth;

class NewConfirmCaseModelController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Creating new case - confirm & submit';

    /** 
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        Admin::script('window.location.replace("' . admin_url("cases") . '");'); return 'Loading...';
     
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
        if (isset($_GET['remove_suspect'])) {
            $remove_suspect = ((int)($_GET['remove_suspect']));
            if ($remove_suspect > 0) {
                $_sus = CaseSuspect::find($remove_suspect);
                if ($_sus != null) {
                    $_sus->delete();
                }
            }
        }


        $pendingCase = Utils::hasPendingCase(Auth::user());

        if ($pendingCase == null) {
            die("Active case not found.");
        }
 
        $form = new Form(new CaseModel());

        $form = new Form(new CaseModel());
        Admin::css(url('/css/new-case.css'));

        $form->disableCreatingCheck();
        $form->disableReset();
        $form->disableEditingCheck();
        $form->disableViewCheck();

        $form->html(view('steps', ['active' => 4,'case' => $pendingCase]));




        $form->divider('CASE INFORMATION');
        $form->html(view('case-confirm', ['case' => $pendingCase]));
        $sus = count($pendingCase->suspects);
        $form->divider("SUSPECTS ($sus) ");
        $form->html(view('case-suspects-confirm', ['case' => $pendingCase]));

        $sus = count($pendingCase->exhibits);

        $form->divider("exhibits ($sus) ");
        $form->html(view('case-exhibits-confirm', ['case' => $pendingCase]));
        $form->divider("confirm & submit"); 

        $form->radio('case_submitted', 'Are you sure you want to submit?')
            ->options([1 => 'Yes'])
            ->rules('required')
            ->required();



        return $form;
    }
}
