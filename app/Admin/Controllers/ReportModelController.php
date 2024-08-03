<?php

namespace App\Admin\Controllers;

use App\Models\ReportModel;
use App\Models\Utils;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Models\PA;

class ReportModelController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Reports';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ReportModel());
        $grid->model()->orderBy('id', 'desc'); 
        $grid->quickSearch('title')->placeholder('Search by title');
        $grid->column('id', __('S/n'))->width(50)->sortable();
        $grid->column('created_at', __('Date Created'))
            ->display(function ($created_at) {
                return Utils::my_date_time_2($created_at);
            })
            ->sortable()
            ->hide();
        $grid->column('updated_at', __('Updated'))->display(function ($updated_at) {
            return Utils::my_date_time_2($updated_at);
        })->sortable()->hide();
        $grid->column('title', __('Title'))->sortable()
            ->limit(50);
        $grid->column('type', __('Report Scope'))->sortable()
            ->using([
                'ca' => 'Specific Conservation area',
                'pa' => 'Specific Protected area',
                'all' => 'Entire Database',
            ])->label([
                'ca' => 'info',
                'pa' => 'success',
                'all' => 'danger',
            ])->filter([
                'ca' => 'Specific Conservation area',
                'pa' => 'Specific Protected area',
                'all' => 'Entire Database',
            ]);
        $grid->column('cases_count', __('Total Cases'))->sortable();
        $grid->column('suspects_count', __('Total Suspects'))->sortable();
        $grid->column('exhibits_count', __('Total Exhibits'))->sortable();
        $grid->column('ca_id', __('C.A'))->hide();
        $grid->column('pa_id', __('P.A'))->sortable()->hide();
        $grid->column('start_date', __('Date Range'))->display(function () {
            return Utils::my_date($this->start_date) . " - " . Utils::my_date($this->end_date);
        })->sortable();
        $grid->column('date_generated', __('Date Denerated'))->sortable();
        $grid->column('is_generated', __('Gerate Now'))
            ->display(function ($is_generated) {
                $url = url('report-generate?id=' . $this->id);
                //new tab
                return "<a href='" . $url . "' target='_blank'>Generate Report Now</a>";
            })->sortable();
        $grid->column('generated_by_id', __('Generated by'))->sortable()->hide();
        $grid->column('pdf_file', __('Pdf File'))
            ->display(function ($pdf_file) {
                $url = url('storage/files/' . $pdf_file);

                //if is_generated is not yes
                if ($this->is_generated != 'Yes') {
                    return "Not generated";
                }

                return "<a href='" . $url . "' target='_blank'>Download Report</a>";
            });
        $grid->column('downloads', __('Downloads'))->sortable()->hide();

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
        $show = new Show(ReportModel::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('title', __('Title'));
        $show->field('cases_count', __('Cases count'));
        $show->field('suspects_count', __('Suspects count'));
        $show->field('exhibits_count', __('Exhibits count'));
        $show->field('type', __('Type'));
        $show->field('ca_id', __('Ca id'));
        $show->field('pa_id', __('Pa id'));
        $show->field('date_type', __('Date type'));
        $show->field('start_date', __('Start date'));
        $show->field('end_date', __('End date'));
        $show->field('is_generated', __('Is generated'));
        $show->field('date_generated', __('Date generated'));
        $show->field('generated_by_id', __('Generated by id'));
        $show->field('pdf_file', __('Pdf file'));
        $show->field('downloads', __('Downloads'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new ReportModel());

        if ($form->isCreating()) {
            $form->hidden('is_generated', __('Is generated'))->default('No');
        }
        if (!$form->isCreating()) {
            $form->text('title', __('Title'))->rules('required');
        }
        $form->radio('type', __('Report Scope'))
            ->options([
                'ca' => 'Specific Conservation area',
                'pa' => 'Specific Protected area',
                'all' => 'Entire Database',
            ])
            ->rules('required')
            ->when('ca', function (Form $form) {
                $form->select('ca_id', __('Conservation Area'))
                    ->options(\App\Models\ConservationArea::all()->pluck('name', 'id'))
                    ->rules('required');
            })
            ->when('pa', function (Form $form) {
                $form->select('pa_id', __('Protected Area'))
                    ->options(PA::all()->pluck('name', 'id'))
                    ->rules('required');
            });

        $form->radio('date_type', __('Date type'))
            ->options([
                'this_week' => 'This week',
                'previous_week' => 'Previous week',
                'last_week' => 'Last week',
                'this_month' => 'This month',
                'previous_month' => 'Previous month',
                'last_month' => 'Last month',
                'this_year' => 'This year',
                'previous_year' => 'Previous year',
                'last_year' => 'Last year',
                'custom' => 'Custom',
            ])
            ->rules('required')
            ->when('custom', function (Form $form) {
                $form->date('start_date', __('Start date'))->default(date('Y-m-d'))->rules('required');
                $form->date('end_date', __('End date'))->default(date('Y-m-d'))->rules('required|after:start_date');
            });

        if (!$form->isCreating()) {
            $form->radio('is_generated', __('Is generated'))->default('No');
        }


        $u = Admin::user();
        $form->hidden('generated_by_id', __('Generated by id'))
            ->default($u->id)
            ->rules('required');
        // $form->textarea('pdf_file', __('Pdf file'));

        if (!$form->isCreating()) {
            $form->radio('is_generated', __('Regenerate Report'))
                ->options([
                    'No' => 'Yes',
                    'Yes' => 'No',
                ])
                ->rules('required');
        }

        return $form;
    }
}
