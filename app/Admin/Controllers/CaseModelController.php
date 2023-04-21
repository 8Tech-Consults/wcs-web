<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\CaseModel\CaseModelActionAddExhibit;
use App\Admin\Actions\CaseModel\CaseModelActionAddSuspect;
use App\Admin\Actions\CaseModel\CaseModelActionEditCase;
use App\Admin\Actions\CaseModel\CaseModelAddComment;
use App\Models\CaseModel;
use App\Models\CaseSuspect;
use App\Models\ConservationArea;
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
                $x = new Exhibit();
                $x->case_id =  $pendingCase->id;
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

        $grid->model()->orderBy('id', 'Desc');


        $u = Auth::user();
        if ($u->isRole('ca-agent')) {
            $grid->model()->where([
                'reported_by' => $u->id
            ]);
            $grid->disableExport();
        } else if (
            $u->isRole('ca-team')
        ) {
            $grid->model()->where([
                'ca_id' => $u->ca_id
            ])->orWhere([
                'reported_by' => $u->id
            ]);
        }

        //if($u->isRole('admin'))


        $grid->export(function ($export) {

            $export->filename('Cases');

            $export->except(['actions']);

            // $export->only(['column3', 'column4' ...]);

            $export->originalValue(['suspects_count', 'exhibit_count']);
        });





        $grid->filter(function ($f) {

            $f->disableIdFilter();
            $f->between('created_at', 'Filter by date')->date();
            $f->equal('reported_by', "Filter by complainant")
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
                    return [ $a->name];
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
        });



        $grid->disableBatchActions();
        //$grid->disableActions();

        $grid->actions(function ($actions) {

            $actions->disableEdit();

            $actions->disableDelete();
            $actions->add(new CaseModelActionAddSuspect);
            $actions->add(new CaseModelActionAddExhibit);
            $actions->add(new CaseModelAddComment);
            if (
                Auth::user()->isRole('hq-team-leaders') ||
                Auth::user()->isRole('ca-team')
            ) {
            } else {
                $actions->add(new CaseModelActionEditCase);
            }
        });



        $grid->quickSearch('title')->placeholder("Search by case title...");


        $grid->column('id', __('ID'))->sortable();
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
            ->required()
            ->rules('required');

        $form->text('officer_in_charge', 'Complainant')->rules('required');


        $form->radio('is_offence_committed_in_pa', __('Did the case take place in a PA?'))
            ->rules('required')
            ->options([
                'Yes' => 'Yes',
                'No' => 'No',
            ])
            ->default(null)
            ->when('No', function (Form $form) {


                $form->select('sub_county_id', __('Sub county'))
                    ->rules('required')
                    ->options(Location::get_sub_counties_array());

                $form->text('parish', __('Parish'))->rules('required');
                $form->text('village', __('Village'))->rules('required');
            })->when('Yes', function (Form $form) {
                $form->select('pa_id', __('Select PA'))
                    ->rules('required')
                    ->options(PA::where('id', '!=', 1)->get()
                        ->pluck('name_text', 'id'));
                $form->text('village', 'Enter location')
                    ->rules('required');
            });


        $form->text('latitude', 'Case scene GPS - latitude');
        $form->text('longitude', 'Case scene GPS - longitude');


        $form->hidden('has_exhibits', __('Does this case have exhibits?'))
            ->default(1);

        $form->select('detection_method', __('Detection method'))
            ->options([
                'Ambush patrol based on Intelligence' => 'Ambush patrol based on Intelligence',
                'Contacted by security agencies' => 'Contacted by security agencies',
                'House visit based on intelligence' => 'House visit based on intelligence',
                'Intelligence led patrol' => 'Intelligence led patrol',
                'Observed during non-duty activities' => 'Observed during non-duty activities',
                'Routine patrol by rangers' => 'Routine patrol by rangers',
                'Routine security check' => 'Routine security check',
                'Investigation' => 'Investigation',
                'Risk profiling' => 'Risk profiling',
                'Random selection' => 'Random selection'
            ])
            ->rules('required');




        return $form;
    }
}
