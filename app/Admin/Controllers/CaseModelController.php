<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\CaseModel\CaseModelActionAddExhibit;
use App\Admin\Actions\CaseModel\CaseModelActionAddSuspect;
use App\Admin\Actions\CaseModel\CaseModelActionEditCase;
use App\Admin\Actions\CaseModel\CaseModelAddComment;
use App\Models\CaseModel;
use App\Models\CaseSuspect;
use App\Models\ConservationArea;
use App\Models\DetectionMethod;
use App\Models\Exhibit;
use App\Models\Location;
use App\Models\Offence;
use App\Models\PA;
use App\Models\TempData;
use App\Models\Utils;
use Carbon\Carbon;
use Encore\Admin\Actions\RowAction;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Widgets\InfoBox;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Auth;


class CaseModelController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Cases';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */





    protected function grid()
    {


        $pendingCase = null;
        if (isset($_GET['add_exhibit_to_case_id'])) {
            $pendingCase = CaseModel::find($_GET['add_exhibit_to_case_id']);
            if ($pendingCase != null) {
                session(['pending_case_id' => $pendingCase->id]);
                $x = new Exhibit();
                $x->reported_by =  Auth::user()->id;
                $x->save();
                Admin::script('window.location.replace("' . admin_url("add-exhibit/{$x->id}/edit") . '");');
                return 'loading...';
            }
        }



        $pendingCase = Utils::hasPendingCase(Auth::user());
        if ($pendingCase != null) {
            Admin::script('window.location.replace("' . admin_url('new-case-suspects/create') . '");');
            return 'Loading...';
        }

        $grid = new Grid(new CaseModel());
        $grid->disableCreateButton();



        // $grid->model()->whereHas('suspects')->orderBy('updated_at', 'DESC'); //TODO("Slowing the query, find a better way")
        $grid->model()->orderBy('updated_at', 'DESC');

        $grid->export(function ($export) {

            $export->filename('Cases');

            $export->except(['actions']);

            // $export->only(['column3', 'column4' ...]);

            $export->originalValue(['suspects_count', 'exhibit_count']);
        });





        $grid->filter(function ($f) {

            $f->disableIdFilter();
            $f->between('created_at', 'Filter by date')->date();
            $f->equal('officer_in_charge', "Filter by complainant");

            $f->equal('reported_by', "Filter by reported by")
                ->select(Administrator::all()->pluck('name', 'id'));


            $f->equal('ca_id', "Filter by CA")
                ->select(ConservationArea::all()->pluck('name', 'id'));

            $ajax_url = url(
                '/api/ajax?'
                    . "&search_by_1=name"
                    . "&search_by_2=id"
                    . "&query_parent=0"
                    . "&model=Location"
            );

            $f->equal('district_id', 'Filter by district')->select(function ($id) {
                $a = Location::find($id);
                if ($a) {
                    return [$a->name];
                }
            })
                ->ajax($ajax_url);


            $ajax_url = url(
                '/api/ajax?'
                    . "&search_by_1=name"
                    . "&search_by_2=id"
                    . "&model=Location"
            );
            $f->equal('sub_county_id', 'Filter by Sub county')->select(function ($id) {
                $a = Location::find($id);
                if ($a) {
                    return [$a->id => "#" . $a->id . " - " . $a->name];
                }
            })
                ->ajax($ajax_url);


            $f->equal('created_by_ca_id', "Filter by CA of Entry")
                ->select(ConservationArea::all()->pluck('name', 'id'));
        });




        $grid->disableBatchActions();
        //$grid->disableActions();

        $grid->actions(function ($actions) {

            $actions->disableEdit();

            $user = Admin::user();
            $row = $actions->row;
            if(!$user->isRole('admin')){
                $actions->disableDelete();
            }

            $can_add_suspect = false;
            $can_add_exhibit = false;
            $can_add_comment = false;
            $can_add_court_info = false;
            $can_add_edit = false;
            if ($user->isRole('ca-agent')) {
                if (
                    $row->reported_by == $user->id
                ) {
                    $can_add_suspect = true;
                    $can_add_exhibit = true;
                    $can_add_comment = true;
                    $can_add_court_info = true;
                }
            } elseif ($user->isRole('ca-team')) {
                if (
                    $row->reported_by == $user->id ||
                    $row->created_by_ca_id == $user->ca_id ||
                    $row->ca_id == $user->ca_id
                ) {
                    $can_add_suspect = true;
                    $can_add_exhibit = true;
                    $can_add_comment = true;
                    $can_add_court_info = true;
                }
            } elseif ($user->isRole('ca-manager')) {
                if (
                    $row->reported_by == $user->id ||
                    $row->created_by_ca_id == $user->ca_id ||
                    $row->ca_id == $user->ca_id
                ) {
                    $can_add_suspect = true;
                    $can_add_exhibit = true;
                    $can_add_comment = true;
                    $can_add_court_info = true;
                    $can_add_edit = true;
                }
            } elseif (
                $user->isRole('hq-team-leaders')
            ) {
                if (
                    $row->reported_by == $user->id ||
                    $row->created_by_ca_id == 1
                ) {
                    $can_add_suspect = true;
                    $can_add_exhibit = true;
                    $can_add_comment = true;
                }
            } elseif ($user->isRole('hq-manager')) {
                $can_add_comment = true;
            } elseif ($user->isRole('director')) {
                $can_add_comment = true;
            } elseif ($user->isRole('secretary')) {
            } elseif (
                $user->isRole('hq-prosecutor')
            ) {
                $can_add_comment = true;
            } elseif ($user->isRole('prosecutor')) {
                if (
                    $row->created_by_ca_id == $user->ca_id
                ) {
                    $can_add_comment = true;
                }
            } else if (
                $user->isRole('admin')
            ) {
                $can_add_suspect = true;
                $can_add_exhibit = true;
                $can_add_comment = true;
                $can_add_court_info = true;
                $can_add_edit = true;
            }

            $is_active  = true;
            $case = $row;
            if (
                true
            ) {
                if (strtolower($case->court_status) == 'concluded') {
                    $is_active = false;
                }
            }
            if (!$is_active) {
                return;
            }


            if ($can_add_suspect) {
                $actions->add(new CaseModelActionAddSuspect);
            }
            if ($can_add_exhibit) {
                $actions->add(new CaseModelActionAddExhibit);
            }
            if ($can_add_comment) {
                $actions->add(new CaseModelAddComment);
            }

            if ($can_add_edit) {
                $actions->add(new CaseModelActionEditCase);
            }

            return;
            dd($this->row);
            dd($user);
            if ($user->isRole('admin') || $user->isRole('hq-team-leaders') || $user->isRole('hq-manager') || $user->isRole('ca-team') || $user->isRole('ca-agent') || $user->isRole('director') || $user->isRole('ca-manager')) {
                if (!$user->isRole('hq-manager') && !$user->isRole('director')) {
                    if ($user->isRole('admin')) {
                        $actions->add(new CaseModelActionAddSuspect);
                        $actions->add(new CaseModelActionEditCase);
                        $actions->add(new CaseModelActionAddExhibit);
                        $actions->add(new CaseModelAddComment);
                    } else {
                        if ($actions->row->reported_by == $user->id) {
                            $actions->add(new CaseModelActionAddSuspect);
                            // $actions->add(new CaseModelActionEditCase);
                            $actions->add(new CaseModelActionAddExhibit);
                            $actions->add(new CaseModelAddComment);
                        }
                    }
                } else { // HQ manager and director
                    $actions->add(new CaseModelAddComment);
                }
            }
        });



        $grid->quickSearch('title')->placeholder("Search by case title...");


        $grid->column('id', __('ID'))->sortable()->hide();
        $grid->column('created_at', __('Created'))
            ->display(function ($x) {
                return Utils::my_date_time($x);
            })
            ->sortable();

        $grid->column('updated_at', __('Updated'))
            ->display(function ($x) {
                return Utils::my_date_time($x);
            })
            ->hide()
            ->sortable();

        $grid->column('case_number', __('Case number'))
            ->sortable();

        $grid->column('title', __('Title'))
            ->sortable();


        $grid->column('ca_id', __('CA'))
            ->display(function () {
                if ($this->ca == null) {
                    return  "-";
                }
                return $this->ca->name;
            })
            ->sortable();
        $grid->column('district_id', __('District'))
            ->hide()
            ->display(function () {
                return $this->district->name;
            })
            ->sortable();

        $grid->column('sub_county_id', __('Sub-county'))
            ->hide()
            ->display(function () {
                if ($this->sub_county == null) {
                    return '-';
                }
                return $this->sub_county->name;
            })
            ->sortable();


        $grid->column('suspects_count', __('Suspects'))->display(function () {
            $link = admin_url('case-suspects?case_id=' . $this->id);
            return '<a data-toggle="tooltip" data-placement="bottom"  title="View suspects" class="text-primary h3" href="' . $link . '" >' . $this->suspects_count . '</a>';
        });
        $grid->column('exhibit_count', __('Exhibits'))->display(function () {
            $link = admin_url('exhibits?case_id=' . $this->id);
            return '<a data-toggle="tooltip" data-placement="bottom"  title="View exhibits" class="text-primary h3" href="' . $link . '" >' . $this->exhibit_count . '</a>';
        });

        $grid->column('reported_by', __('Reported by'))
            ->display(function () {
                return $this->reportor->name;
            })
            ->sortable();



        /*  $grid->column('actions', __('Actions'))->display(function () {
            $view_link = '<a class="" href="' . url("cases/{$this->id}") . '">
                <i class="fa fa-eye"></i> View case details</a>';

            $suspetcs_link = '<br><a class="" href="' . url("case-suspects?case_id={$this->id}") . '">
                <i class="fa fa-users"></i> View case suspetcs</a>';
            $suspetcs_link = "";

            $edit_link = '<br> <a class="" href="' . url("cases/{$this->id}/edit") . '">
                <i class="fa fa-edit"></i> Edit case info</a>';

            $add_link = '<br> <a class="" href="' . url("case-suspects/create?case_id={$this->id}") . '">
                <i class="fa fa-user-plus"></i> Add case suspect</a>';

            return $view_link . $suspetcs_link . $edit_link . $add_link;
        }); */

        //created_by_ca_id
        $grid->column('created_by_ca_id', __('CA of Entry'))
            ->display(function () {
                if ($this->created_by_ca == null) {
                    return  "-";
                }
                return $this->created_by_ca->name;
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
        $c = CaseModel::findOrFail($id);

        return view('admin.case-details', [
            'c' => $c
        ]);

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

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */


    protected function form()
    {


        if (isset($_GET['refresh_page'])) {
            $r = (int)($_GET['refresh_page']);
            if ($r == 1) {
                Admin::script('window.location.replace("' . admin_url('new-case/create') . '");');
                return 'Loading...';
            }
        }

        $form = new Form(new CaseModel());



        $form->disableCreatingCheck();
        $form->disableReset();
        $form->disableEditingCheck();
        $form->disableViewCheck();


        $form->hidden('reported_by', __('Reported by'))->default(Admin::user()->id)->rules('required');



        $form->text('title', __('Case title'))
            ->help("Describe this case in summary")
            ->rules('required');
        $form->textarea('offence_description', __('Case description'))
            ->help("Describe this case in details")
            ->rules('required');

        $form->date('case_date', 'Date when opened')
            ->rules('required');

        $form->text('officer_in_charge', 'Complainant')->rules('required');


        $form->radio('is_offence_committed_in_pa', __('Did the case take place in a PA?'))
            ->rules('required')
            ->options([
                'Yes' => 'Yes',
                'No' => 'No',
            ])
            ->when('No', function (Form $form) {


                $form->select('sub_county_id', __('Sub county'))
                    ->rules('required')
                    ->options(Location::get_sub_counties_array());

                $form->text('parish', __('Parish'));
                $form->text('village', __('Village'));
            })->when('Yes', function (Form $form) {
                $form->select('pa_id', __('Select PA'))
                    ->rules('required')
                    ->options(PA::where('id', '!=', 1)->get()
                        ->pluck('name_text', 'id'));
                $form->text('village', 'Enter location');
            });


        $form->text('latitude', 'Case scene GPS - latitude');
        $form->text('longitude', 'Case scene GPS - longitude');


        $form->hidden('has_exhibits', __('Does this case have exhibits?'))
            ->default(1);

        $form->select('detection_method', __('Detection method'))
            ->options(
                DetectionMethod::pluck('name', 'name')
            )
            ->rules('required');

        return $form;
    }
}
