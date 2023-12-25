<?php

namespace App\Admin\Controllers;

use App\Models\CaseModel;
use App\Models\CaseSuspect;
use App\Models\SuspectLink;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class SuspectLinkController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Repeat Offenders';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new SuspectLink());

        //filter
        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->like('suspect_id_1', 'Suspect ID #1');
            $filter->like('suspect_id_2', 'Suspect ID #2');
            $filter->like('case_id_1', 'Case ID #1');
            $filter->like('case_id_2', 'Case ID #2');
        });

        $grid->disableBatchActions();
        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->disableActions();

        $grid->model()->orderBy('id', 'desc');
        $grid->column('created_at', __('Date'))
            ->display(function ($created_at) {
                return date('d-m-Y', strtotime($created_at));
            })
            ->sortable();
        $grid->column('suspect_id_1', __('Suspect #1'))
            ->display(function ($suspect_id_2) {
                $suspect = CaseSuspect::find($suspect_id_2);
                if ($suspect == null) {
                    return "Suspect not found";
                }
                $name =  $suspect->first_name . " " . $suspect->last_name;
                $view_text = "<a title=\"View Suspect\" href='" . admin_url('case-suspects/' . $suspect_id_2) . "' target='_blank'><b>$name</b></a>";
                return $view_text;
            })
            ->sortable();
        $grid->column('suspect_id_2', __('Suspect #2'))
            ->display(function ($suspect_id_2) {
                $suspect = CaseSuspect::find($suspect_id_2);
                if ($suspect == null) {
                    return "Suspect not found";
                }
                $name =  $suspect->first_name . " " . $suspect->last_name;
                $view_text = "<a title=\"View Suspect\" href='" . admin_url('case-suspects/' . $suspect_id_2) . "' target='_blank'><b>$name</b></a>";
                return $view_text;
            })
            ->sortable();

        $grid->column('case_id_1', __('Case #1'))
            ->display(function ($case_id_1) {
                $case = CaseModel::find($case_id_1);
                if ($case == null) {
                    return "Case not found";
                }
                //do the same for case
                $view_text = "<a title=\"View Case\" href='" . admin_url('cases/' . $case_id_1) . "' target='_blank'><b>$case->case_number</b></a>";
                return $view_text;
            })
            ->sortable();

        $grid->column('case_id_2', __('Case #2'))
            ->display(function ($case_id_2) {
                $case = CaseModel::find($case_id_2);
                if ($case == null) {
                    return "Case not found";
                }
                //do the same for case
                $view_text = "<a title=\"View Case\" href='" . admin_url('cases/' . $case_id_2) . "' target='_blank'><b>$case->case_number</b></a>";
            })
            ->sortable();

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
        $show = new Show(SuspectLink::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('suspect_id_1', __('Suspect id 1'));
        $show->field('suspect_id_2', __('Suspect id 2'));
        $show->field('case_id_1', __('Case id 1'));
        $show->field('case_id_2', __('Case id 2'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new SuspectLink());

        $form->text('suspect_id_1', __('Suspect id 1'));
        $form->text('suspect_id_2', __('Suspect id 2'));
        $form->text('case_id_1', __('Case id 1'));
        $form->text('case_id_2', __('Case id 2'));

        return $form;
    }
}
