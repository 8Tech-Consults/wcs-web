<?php

namespace App\Admin\Controllers;

use App\Models\CaseModel;
use App\Models\CaseSuspect;
use App\Models\Location;
use App\Models\PA;
use App\Models\Utils;
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

        $grid = new Grid(new CaseModel());
        $grid->model()->orderBy('id', 'Desc');



        $grid->filter(function ($f) {
            // Remove the default id filter
            $f->disableIdFilter();
            $f->between('created_at', 'Filter by date')->date();
            $f->equal('reported_by', "Filter by reporter")
                ->select(Administrator::all()->pluck('name', 'id'));

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
                    return [$a->id => "#" . $a->id . " - " . $a->name];
                }
            })
                ->ajax($ajax_url);


            $f->equal('status', 'Filter case status')->select([
                0 => 'Pending',
                1 => 'Active',
                2 => 'Closed',
            ]);
        });



        $grid->disableBatchActions();
        $grid->disableActions();
        $grid->actions(function ($actions) {
            $actions->disableDelete();
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


        $grid->column('district_id', __('District'))
            ->display(function () {
                return $this->district->name;
            })
            ->sortable();

        $grid->column('sub_county_id', __('Sub-county'))
            ->display(function () {
                return $this->sub_county->name;
            })
            ->sortable();


        $grid->column('suspects', __('Suspects'))->display(function () {
            return count($this->suspects);
        });
        $grid->column('exhibits', __('Exhibits'))->display(function () {
            return count($this->suspects);
        });

        $grid->column('reported_by', __('Reported by'))
            ->display(function () {
                return $this->reportor->name;
            })
            ->sortable();
        $grid->column('status', __('Status'))
            ->sortable()
            ->using([
                0 => 'Pending',
                1 => 'Active',
                2 => 'Closed',
            ], 'Not in Court')->label([
                null => 'warning',
                0 => 'warning',
                1 => 'success',
                2 => 'danger',
            ], 'danger');


        $grid->column('actions', __('Actions'))->display(function () {
            $view_link = '<a class="" href="' . url("cases/{$this->id}") . '">
                <i class="fa fa-eye"></i> View case details</a>';

            $suspetcs_link = '<br><a class="" href="' . url("all-suspects?case_id={$this->id}") . '">
                <i class="fa fa-users"></i> View case suspetcs</a>';

            $edit_link = '<br> <a class="" href="' . url("cases/{$this->id}/edit") . '">
                <i class="fa fa-edit"></i> Edit case info</a>';

            $add_link = '<br> <a class="" href="' . url("case-suspects/create?case_id={$this->id}") . '">
                <i class="fa fa-user-plus"></i> Add case suspect</a>';
            return $view_link . $suspetcs_link . $edit_link . $add_link;
        });
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
        $form = new Form(new CaseModel());


        $form->disableCreatingCheck();
        $form->disableReset();
        $form->disableEditingCheck();

        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
        });



        if ($form->isCreating()) {
            $form->hidden('reported_by', __('Reported by'))->default(Admin::user()->id)->rules('int|required');
        }

        $form->tab('Offence', function (Form $form) {

            $form->text('title', __('Offence title'))
                ->help("Describe this offence in summary")
                ->rules('required');


            $form->select('sub_county_id', __('Sub county'))
                ->rules('int|required')
                ->options(Location::get_sub_counties()->pluck('name_text', 'id'));
            $form->text('parish', __('Parish'))->rules('required');
            $form->text('village', __('Village'))->rules('required');
            $form->hidden('offence_category_id', __('Village'))->default(1)->value(1);

            /*
            $form->select('offence_category_id', __('Offence category'))
            offence_category_id
                ->rules('int|required')  
                ->options([  
                    1 => 'Type 1',
                    2 => 'Type 2',
                    3 => 'Type 3',
                    4 => 'Type 4',
                ]); 
            */



            $form->textarea('offence_description', __('Offence description'))->rules('required');


            $form->radio('is_offence_committed_in_pa', __('Is offence committed within a PA?'))
                ->rules('int|required')
                ->options([
                    1 => 'Yes',
                    0 => 'No',
                ])
                ->default(0)
                ->when(1, function (Form $form) {

                    $form->select('pa_id', __('Select PA'))
                        ->rules('int|required')
                        ->options(PA::all()->pluck('name_text', 'id'));
                });


            $form->hidden('has_exhibits', __('Does this case have exhibits?'))
                ->default(1);


            $form->select('status', __('Offence status'))
                ->rules('int|required')
                ->options([
                    1 => 'Pending for verification',
                    2 => 'Active',
                    3 => 'Closed',
                ]);


            if ($form->isCreating()) {
                $form->select('status', __('Status'))
                    ->options([
                        1 => 'Save as draft',
                        2 => 'Submit case for approval',
                        0 => 'No',
                    ])
                    ->default(1);
            }
        });

        if ($form->isCreating()) {
            $form->tab('Suspects', function (Form $form) {
                $form->morphMany('suspects', 'Click on new to add suspect', function (Form\NestedForm $form) {


                    $subs = Location::get_sub_counties_array();

                    $form->image('photo', 'Suspect photo');
                    $form->divider('Suspect bio data');
                    $form->text('first_name')->rules('required');
                    $form->text('middle_name');
                    $form->text('last_name')->rules('required');
                    $form->text('uwa_suspect_number')->rules('required');
                    $form->radio('sex')->options([
                        'Male' => 'Male',
                        'Female' => 'Female',
                    ])->rules('required');
                    $form->date('age', 'Date of birth')->rules('required');
                    $form->mobile('phone_number')->options(['mask' => '999 9999 9999']);
                    $form->text('national_id_number');
                    $form->text('occuptaion');


                    $form->select('country')
                        ->help('Nationality of the suspect')
                        ->options(Utils::COUNTRIES())->rules('required');


                    $form->select('sub_county_id', __('Sub county'))
                        ->rules('int|required')
                        ->help('Where this suspect originally lives')
                        ->options(Location::get_sub_counties()->pluck('name_text', 'id'));
                    $form->text('parish');
                    $form->text('village');
                    $form->text('ethnicity');

                    $form->divider('Arrest information');
                    $form->radio('use_same_arrest_information', "Do you want to use this arrest information for rest of suspects?")
                        ->options([
                            1 => 'Yes (Use this arrest information for all asuspects)',
                            0 => 'No (Don\'t Use this arrest information for all asuspects)',
                        ])
                        ->rules('required');


                    $form->datetime('arrest_date_time', 'Arrest date and time');
                    $form->select('arrest_sub_county_id', __('Arrest Sub county'))
                        ->help('Where this suspect was arrested')
                        ->options($subs);

                    $form->text('arrest_parish', 'Arrest parish');
                    $form->text('arrest_village', 'Arrest vaillage');

                    /* $form->latlong('arrest_latitude', 'arrest_longitude', 'Arrest location on map')->height(500)->rules('required'); */
                    $form->text('arrest_first_police_station', 'Arrest police station');
                    $form->text('arrest_current_police_station', 'Current police station');
                    $form->text('arrest_agency', 'Arrest agency');
                    $form->text('arrest_uwa_unit', 'UWA Unit');
                    $form->text('arrest_detection_method', 'Arrest detection method');
                    $form->text('arrest_uwa_number', 'UWA Arest number');
                    $form->text('arrest_crb_number', 'CRB number');

                    $form->divider('Court information');


                    $form->radio('use_same_court_information', "Do you want to use this court information for rest of suspects?")
                        ->options([
                            1 => 'Yes (Use this court information for all asuspects)',
                            0 => 'No (Don\'t Use this court information for all asuspects)',
                        ])
                        ->rules('required');

                    $form->date('court_date', 'Court date');
                    $form->text('prosecutor', 'Names of the prosecutors');
                    $form->radio('is_convicted', __('Has suspect been convicted?'))
                        ->options([
                            1 => 'Yes',
                            0 => 'No',
                        ]);

                    $form->text('case_outcome', 'Case outcome');
                    $form->text('magistrate_name', 'Magistrate Name');
                    $form->text('court_name', 'Court Name');
                    $form->text('court_file_number', 'Court file number');

                    $form->radio('is_jailed', __('Has suspect been jailed?'))
                        ->options([
                            1 => 'Yes',
                            0 => 'No',
                        ]);
                    $form->date('jail_date', 'Jail date');
                    $form->decimal('jail_period', 'Jail period')->help("(In months)");

                    $form->radio('is_fined', __('Has suspect been fined?'))
                        ->options([
                            1 => 'Yes',
                            0 => 'No',
                        ]);
                    $form->decimal('fined_amount', 'File amount')->help("(In UGX)");
                });
            });
        }






        return $form;
    }
}
