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
            $f->between('created_at', 'Filter by date')->date();
            /*             $f->equal('reported_by', "Filter by reporter")
                ->select(Administrator::all()->pluck('name', 'id')); */

            $ajax_url = url(
                '/api/ajax?'
                    . "&search_by_1=title"
                    . "&search_by_2=id"
                    . "&model=CaseModel"
            );

            $f->equal('case_id', 'Filter by case')->select(function ($id) {
                $a = CaseModel::find($id);
                if ($a) {
                    return [$a->id => "#" . $a->id . " - " . $a->title];
                }
            })
                ->ajax($ajax_url);
            $f->like('uwa_suspect_number', 'Filter by UWA Suspect number');

            $f->equal('country', 'Filter country of origin')->select(
                Utils::COUNTRIES()
            );


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


            $f->equal('is_suspects_arrested', 'Filter by arrest status')->select([
                0 => 'Not arrested',
                1 => 'Arrested',
            ]);

            $f->equal('is_suspect_appear_in_court', 'Filter by court status')->select([
                0 => 'Not in court',
                1 => 'In court',
            ]);

            $f->equal('is_convicted', 'Filter by conviction status')->select([
                0 => 'Not Convicted',
                1 => 'Convicted',
            ]);

            $f->equal('is_jailed', 'Filter by jail status')->select([
                0 => 'Not jailed',
                1 => 'Jailed',
            ]);
        });




        $grid->model()->orderBy('id', 'Desc');
        $grid->quickSearch('first_name')->placeholder('Search by first name..');

        $grid->column('id', __('ID'))->sortable()->hide();
        $grid->column('created_at', __('Date'))
            ->display(function ($x) {
                return Utils::my_date_time($x);
            })
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
        $grid->column('is_suspects_arrested', __('Arrest'))
            ->sortable()
            ->using([
                0 => 'Not arrested',
                1 => 'Arrested',
            ], 'Not arrested')->label([
                null => 'danger',
                0 => 'danger',
                1 => 'success',
            ], 'danger');






        $grid->column('action', __('Actions'))->display(function () {

            $view_link = '<a class="" href="' . url("case-suspects/{$this->id}") . '">
            <i class="fa fa-eye"></i>View</a>';
            $edit_link = '<br> <a class="" href="' . url("case-suspects/{$this->id}/edit") . '">
            <i class="fa fa-edit"></i> Edit</a>';
            return $view_link . $edit_link;
        });
        $grid->actions(function ($actions) {
            $actions->disableDelete();
        });
        return $grid;
    }
}
