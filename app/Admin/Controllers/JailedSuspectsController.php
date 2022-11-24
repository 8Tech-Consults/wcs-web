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

class JailedSuspectsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Jailed suspects';

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
                'is_jailed' => 1
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

            $f->equal('is_fined', 'Filter by fine')
                ->select([
                    0 => 'Not fined',
                    1 => 'Fined',
                ]);

            $f->group('fined_amount', 'Filter by fine amount', function ($group) {
                $group->gt('greater than');
                $group->lt('less than');
                $group->equal('equal to');
            });

            $f->group('jail_period', 'Filter by jail period (in Months)', function ($group) {
                $group->gt('greater than');
                $group->lt('less than');
                $group->equal('equal to');
            });

            $f->between('jail_date', 'Filter by jail date')->date();
            $f->like('court_name', 'Filter by court');
            $f->like('prosecutor', 'Filter by prosecutor');
            $f->like('magistrate_name', 'Filter by magistrate');
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


        $grid->column('is_fined', __('Fined'))
            ->sortable()
            ->using([
                0 => 'Not fined',
                1 => 'Fined',
            ], 'Not fined')->label([
                null => 'danger',
                0 => 'danger',
                1 => 'success',
            ], 'danger');

        $grid->column('fined_amount', __('Fine fee'))
            ->display(function ($x) {
                $x = (int)($x);
                if ($x < 1) {
                    return "-";
                }
                return "UGX " . number_format($x);
            })
            ->sortable();

        $grid->column('is_jailed', __('Jail status'))
            ->sortable()
            ->using([
                0 => 'Not jailed',
                1 => 'Jailed',
            ], 'Not arrested')->label([
                null => 'danger',
                0 => 'danger',
                1 => 'success',
            ], 'danger');

        $grid->column('jail_date', __('Jail date'))
            ->display(function ($x) {
                return Utils::my_date($x);
            })
            ->sortable();

        $grid->column('jail_period', __('Jail period'))
            ->display(function ($x) {
                $x = (int)($x);
                if ($x < 1) {
                    return "-";
                }
                return $x . " Months";
            })
            ->sortable();

        $grid->column('court_name', __('Court'))
            ->sortable();
        $grid->column('prosecutor', __('Prosecutor'))
            ->sortable();
        $grid->column('magistrate_name', __('Magistrate'))
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
