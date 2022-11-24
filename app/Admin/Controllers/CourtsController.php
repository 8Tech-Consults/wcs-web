<?php

namespace App\Admin\Controllers;

use App\Models\CaseModel;
use App\Models\CaseSuspect;
use App\Models\Location;
use App\Models\Utils;
use Dflydev\DotAccessData\Util;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Faker\Factory as Faker;

class CourtsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Court cases';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {

        $grid = new Grid(new CaseSuspect());
        $grid->disableBatchActions();
        $grid->disableCreateButton();
        $grid->disableActions();

        $grid->model()
            ->where([
                'is_suspect_appear_in_court' => 1
            ])->orderBy('id', 'Desc');


        $grid->filter(function ($f) {
            // Remove the default id filter
            $f->disableIdFilter();

            $ajax_url = url(
                '/api/ajax?'
                    . "&search_by_1=title"
                    . "&search_by_2=id"
                    . "&model=CaseModel"
            );
            $district_ajax_url = url(
                '/api/ajax?'
                    . "&search_by_1=name"
                    . "&search_by_2=id"
                    . "&query_parent=0"
                    . "&model=Location"
            );

            $f->equal('case_id', 'Filter by offence')->select(function ($id) {
                $a = CaseModel::find($id);
                if ($a) {
                    return [$a->id => "#" . $a->id . " - " . $a->title];
                }
            })
                ->ajax($ajax_url);

            $f->between('court_date', 'Filter by arrest date')->date();
            $f->like('court_name', 'Filter by court name');
            $f->like('court_file_number', 'Filter by court file number');
            $f->like('prosecutor', 'Filter by prosecutor');
            $f->like('magistrate_name', 'Filter by magistrate');
            $f->equal('is_convicted', 'Filter by conviction')
                ->select([
                    0 => 'Not Convicted',
                    1 => 'Convicted',
                ]);
        });


        $grid->model()->orderBy('id', 'Desc');
        $grid->quickSearch('first_name')->placeholder('Search by first name..');

        $grid->column('id', __('Suspect ID'))->sortable();
        $grid->column('created_at', __('Reported'))
            ->display(function ($x) {
                return Utils::my_date_time($x);
            })
            ->hide()
            ->sortable();


        $grid->column('photo_url', __('Photo'))
            ->width(60)
            ->lightbox(['width' => 60, 'height' => 80]);
        $grid->column('updated_at', __('Updated'))
            ->display(function ($x) {
                return Utils::my_date_time($x);
            })
            ->sortable()->hide();



        $grid->column('first_name', __('Name'))
            ->display(function ($x) {
                return $this->first_name . " " . $this->middle_name . " " . $this->last_name;
            })
            ->sortable();
        $grid->column('case_id', __('Offence'))
            ->display(function ($x) {
                return $this->case->title;
            })
            ->sortable();


        $grid->column('is_suspect_appear_in_court', __('Court status'))
            ->sortable()
            ->using([
                0 => 'Not in court',
                1 => 'In court',
            ], 'Not arrested')->label([
                null => 'danger',
                0 => 'danger',
                1 => 'success',
            ], 'danger');

        $grid->column('court_date', __('Court date'))
            ->display(function ($x) {
                return Utils::my_date($x);
            })
            ->sortable();

        $grid->column('court_name', __('Court name'))
            ->sortable();

        $grid->column('court_file_number', __('Court file number'))
            ->sortable();

        $grid->column('prosecutor', __('Prosecutor'))
            ->sortable();

        $grid->column('magistrate_name', __('Magistrate name'))
            ->sortable();



        $grid->column('is_convicted', __('Conviction status'))
            ->sortable()
            ->using([
                0 => 'Not Convicted',
                1 => 'Convicted',
            ], 'Not arrested')->label([
                null => 'danger',
                0 => 'danger',
                1 => 'success',
            ], 'danger');

        $grid->column('case_outcome', __('Case outcome'))
            ->sortable();
        $grid->column('action', __('Actions'))->display(function () {

            $view_link = '<a class="" href="' . url("case-suspects/{$this->id}") . '">
            <i class="fa fa-eye"></i>View</a>';
            $edit_link = '<br><br><a class="" href="' . url("case-suspects/{$this->id}/edit") . '">
            <i class="fa fa-edit"></i> Edit</a>';
            return $view_link . $edit_link;
        });
        $grid->actions(function ($actions) {
            $actions->disableDelete();
        });
        return $grid;
    }
}
