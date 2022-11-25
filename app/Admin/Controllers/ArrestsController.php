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

class ArrestsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Arrests';

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
                'is_suspects_arrested' => 1
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

            $f->equal('case_id', 'Filter by Case')->select(function ($id) {
                $a = CaseModel::find($id);
                if ($a) {
                    return [$a->id => "#" . $a->id . " - " . $a->title];
                }
            })
                ->ajax($ajax_url);

            $f->between('arrest_date_time', 'Filter by arrest date')->date();
            $f->like('arrest_uwa_number', 'Filter by UWA Arrest number');



            $f->equal('arrest_district_id', 'Filter by arrest district')->select(function ($id) {
                $a = Location::find($id);
                if ($a) {
                    return [$a->id => "#" . $a->id . " - " . $a->name];
                }
            })
                ->ajax($district_ajax_url);

            $f->like('arrest_current_police_station', 'Filter by current police station');
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
        $grid->column('case_id', __('Case'))
            ->display(function ($x) {
                return $this->case->title;
            })
            ->sortable();
        $grid->column('is_suspects_arrested', __('Arrest status'))
            ->sortable()
            ->using([
                0 => 'Not arrested',
                1 => 'Arrested',
            ], 'Not arrested')->label([
                null => 'danger',
                0 => 'danger',
                1 => 'success',
            ], 'danger');

        $grid->column('arrest_uwa_number', __('UWA arrest number'))
            ->sortable();

        $grid->column('arrest_date_time', __('Arrest Date'))
            ->display(function ($x) {
                return Utils::my_date_time($x);
            })
            ->sortable();

        $grid->column('arrest_district_id', __('District'))
            ->display(function ($x) {
                return Utils::get('App\Models\Location', $this->arrest_district_id)->name_text;
            })
            ->sortable();
        $grid->column('arrest_sub_county_id', __('Sub-county'))
            ->display(function ($x) {
                return Utils::get(Location::class, $this->arrest_sub_county_id)->name_text;
            })
            ->sortable();

        $grid->column('arrest_current_police_station', __('Police station'))
            ->sortable();
        $grid->column('arrest_detection_method', __('Detection method'))
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
