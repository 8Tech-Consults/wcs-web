<?php

namespace App\Admin\Controllers;

use App\Models\CaseModel;
use App\Models\CaseSuspect;
use App\Models\ConservationArea;
use App\Models\Location;
use App\Models\Offence;
use App\Models\PA;
use App\Models\User;
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
            $f->equal('reported_by', "Filter by complainant")
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
            $link = admin_url('case-suspects', ['case_id' => $this->id]);
            return '<a data-toggle="tooltip" data-placement="bottom"  title="View suspects" class="text-primary h3" href="' . $link . '" >' . count($this->suspects) . '</a>';
        });
        $grid->column('exhibits', __('Exhibits'))->display(function () {
            $link = admin_url('exhibits');
            return '<a data-toggle="tooltip" data-placement="bottom"  title="View exhibits" class="text-primary h3" href="' . $link . '" >' . count($this->exhibits) . '</a>';
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

            $suspetcs_link = '<br><a class="" href="' . url("case-suspects?case_id={$this->id}") . '">
                <i class="fa fa-users"></i> View case suspetcs</a>';
            $suspetcs_link = "";

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
        //$form->disableEditingCheck();





        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
        });



        if ($form->isCreating()) {
            $form->hidden('reported_by', __('Reported by'))->default(Admin::user()->id)->rules('required');
        }

        $form->tab('Offence', function (Form $form) {


            $form->listbox('offences', 'Offences')->options(Offence::all()->pluck('name', 'id'))
                ->help("Select offences involded in this case")
                ->rules('required');


            $form->text('title', __('Offence description'))
                ->help("Describe this case in summary")
                ->rules('required');


            $form->select('offense_category', __('Offence category'))
                ->options([
                    'Category 1' => 'Category 1',
                    'Category 2' => 'Category 2',
                    'Category 3' => 'Category 3',
                ])
                ->rules('required');

            $form->radio('is_offence_committed_in_pa', __('Is offence committed within a PA?'))
                ->rules('required')
                ->options([
                    1 => 'Yes',
                    0 => 'No',
                ])
                ->default(null)
                ->when('No', function (Form $form) {

                    $form->select('ca_id', __('Nearest conservation area'))
                        ->rules('required')
                        ->options(ConservationArea::all()->pluck('name', 'id'));


                    $form->select('sub_county_id', __('Sub county'))
                        ->rules('required')
                        ->options(Location::get_sub_counties_array());

                    $form->text('parish', __('Parish'))->rules('required');
                    $form->text('village', __('Village'))->rules('required'); 
                    $form->hidden('offence_category_id', __('Village'))->default(1)->value(1);
                })->when('Yes', function (Form $form) {
                    $form->select('pa_id', __('Select PA'))
                        ->rules('required')
                        ->options(PA::all()->pluck('name_text', 'id'));
                    $form->text('village', 'Enter location')->rules('required'); 
                });






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
        });


        if ($form->isCreating()) {
            $form->tab('Case details', function (Form $form) {



                $form->text('suspect_first_name')->rules('required');

                $form->html('<button class="btn btn-primary" id="add_anothe_suspect" >ADD  ANOTHER SUSPECT</button>');

                $form->divider();
                $form->html('<div class="container" id="suspects_added" >
                    <h2>SUSPECTS</h2>
                </button>');



                $form->morphMany('suspects', 'Click on new to add suspect', function (Form\NestedForm $form) {



                    $subs = Location::get_sub_counties_array();

                    $form->divider('Suspect bio data');
                    $form->image('photo', 'Suspect photo');
                    $form->text('first_name')->rules('required');
                    $form->text('middle_name');
                    $form->text('last_name')->rules('required');
                    $form->select('sex')->options([
                        'Male' => 'Male',
                        'Female' => 'Female',
                    ])->rules('required');
                    $form->date('age', 'Date of birth')->rules('required');
                    $form->mobile('phone_number')->options(['mask' => '999 9999 9999']);
                    $form->text('national_id_number');
                    $form->text('Occupation');



                    $form->select('is_ugandan', __('Is the suspect a Ugandan'))
                        ->options([
                            'Ugandan' => 'Yes',
                            'Not Ugandan' => 'No',
                        ])
                        ->when('Ugandan', function ($form) {
                            $form->select('country')
                                ->help('Nationality of the suspect')
                                ->options([
                                    'Uganda' => 'Uganda'
                                ])
                                ->default('Uganda')
                                ->readonly()
                                ->rules('required');

                            $form->select('sub_county_id', __('Sub county'))
                                ->rules('required')
                                ->help('Suspect’s place of residence')
                                ->options(Location::get_sub_counties_array());


                            $form->text('parish');
                            $form->text('village');

                            $form->text('ethnicity');
                        })->when('Not Ugandan', function ($form) {
                            $form->select('country')
                                ->help('Nationality of the suspect')
                                ->options(Utils::COUNTRIES())->rules('required');
                        })->rules('required');

                    $form->divider('Offences');

                    $form->listbox('offences', 'Offences')->options(Offence::all()->pluck('name', 'id'))
                        ->help("Select offences involded in this case")
                        ->rules('required');






                    $form->divider('Arrest information');
                    $form->radio('use_same_arrest_information', "Do you want to use this arrest information for rest of suspects?")
                        ->options([
                            1 => 'Yes (Use this arrest information for all asuspects)',
                            0 => 'No (Don\'t Use this arrest information for all asuspects)',
                        ])
                        ->default(0)
                        ->rules('required');


                    $form->datetime('arrest_date_time', 'Arrest date and time')->rules('required');

                    $form->radio('arrest_in_pa', "Was suspect arrested within a P.A")
                        ->options([
                            'Yes' => 'Yes',
                            'No' => 'No',
                        ])
                        ->rules('required');


                    $form->select('pa_id', __('Select PA'))
                        ->options(PA::all()->pluck('name_text', 'id'));
                    $form->text('arrest_village', 'Enter arrest location')->rules('required');


                    $subs = Location::get_sub_counties_array();
                    $form->select('arrest_sub_county_id', __('Sub county of Arrest'))
                        ->help('Where this suspect was arrested')
                        ->options($subs);


                    $form->text('arrest_parish', 'Parish of Arrest');
                    $form->text('arrest_village', 'Village of Arrest');
                    $form->text('arrest_latitude', 'Arrest GPS - latitude');
                    $form->text('arrest_longitude', 'Arrest GPS - longitude');


                    $form->text('arrest_first_police_station', 'Police station of Arrest');
                    $form->text('arrest_current_police_station', 'Current police station');
                    $form->select('arrest_agency', 'Arresting agency')->options([
                        'UWA' => 'UWA',
                        'UPDF' => 'UPDF',
                        'UPF' => 'UPF',
                        'ESO' => 'ESO',
                        'ISO' => 'ISO',
                        'URA' => 'URA',
                        'DCIC' => 'DCIC',
                        'INTERPOL' => 'INTERPOL',
                        'UCAA' => 'UCAA',
                        'Other' => 'Other',
                    ]);

                    $form->select('arrest_uwa_unit', 'UWA Unit')->options([
                        'Canine Unit' => 'The Canine Unit',
                        'WCU' => 'WCU',
                        'LEU' => 'LEU',
                    ]);

                    $form->text('arrest_crb_number', 'CRB number');
                    $form->select('management_action', 'Action taken by management')->options([
                        'Fined' => 'Fined',
                        'Cautioned' => 'Cautioned',
                        'Police bond' => 'Police bond',
                        'Skipped bond' => 'Skipped bond'
                    ])->rules('required');

                    $form->select('is_suspect_appear_in_court', __('Has this suspect appeared in court?'))
                        ->options([
                            'Yes' => 'Yes',
                            'No' => 'No',
                        ])
                        ->when(1, function ($form) {
                            $form->date('created_at', 'Court date')->rules('required');
                        })
                        ->rules('required');

                    $form->divider('Court information');

                    $form->radio('use_same_court_information', "Do you want to use this court information for rest of suspects?")
                        ->options([
                            1 => 'Yes (Use this court information for all asuspects)',
                            0 => 'No (Don\'t Use this court information for all asuspects)',
                        ])
                        ->default(0)
                        ->rules('required');


                    $form->text('court_file_number', 'Court file number');
                    $form->date('court_date', 'Court date')->rules('required');
                    $form->text('court_name', 'Court Name');

                    /*  $form->select('prosecutor', 'Lead prosecutor')
                    ->options(function ($id) {
                        $a = User::find($id);
                        if ($a) {
                            return [$a->id => "#" . $a->id . " - " . $a->name];
                        }
                    })
                    ->ajax(url(
                        '/api/ajax?'
                            . "&search_by_1=name"
                            . "&search_by_2=id"
                            . "&model=User"
                    ))->rules('required');  */

                    $form->text('prosecutor', 'Lead prosecutor');
                    $form->text('magistrate_name', 'Magistrate Name');

                    $form->select('status', __('Case status'))
                        ->rules('required')
                        ->options([
                            'On-going investigation' => 'On-going investigation',
                            'Closed' => 'Closed',
                            'Re-opened' => 'Re-opened',
                        ]);

                    $form->select('case_outcome', 'Specific case status')->options([
                        'Charged' => 'Charged',
                        'Remand' => 'Remand',
                        'Bail' => 'Bail',
                        'Perusal' => 'Perusal',
                        'Further investigation' => 'Further investigation',
                        'Dismissed' => 'Dismissed',
                        'Convicted' => 'Convicted',
                    ]);
                    $form->radio('is_jailed', __('Was the Accused jailed?'))
                        ->options([
                            'Yes' => 'Yes',
                            'No' => 'No',
                        ]);
                    $form->date('jail_date', 'Sentence date')
                        ->rules('required');
                    $form->decimal('jail_period', 'Jail period')->help("(In months)")
                        ->rules('required');

                    $form->radio('is_fined', __('Was the Accused fined?'))
                        ->options([
                            'Yes' => 'Yes',
                            'No' => 'No',
                        ])->when('Yes', function ($form) {
                            $form->decimal('fined_amount', 'Fine amount')->help("(In UGX)")
                                ->rules('required'); 
                        });

                    $form->radio('community_service', __('Was the Accused issued a community service?'))
                        ->options([
                            'Yes' => 'Yes',
                            'No' => 'No',
                        ])
                        ->when('Yes', function ($form) {
                            $form->date('created_at', 'Court date')->rules('required');
                        });
                });
            });
        }




        $form->tab('Exhibits', function (Form $form) {
            $form->morphMany('exhibits', 'Click on new to add exhibit', function (Form\NestedForm $form) {

                $form->select('exhibit_catgory', __('Exhibit category'))
                    ->options([
                        'Wildlife' => 'Wildlife',
                        'Implements' => 'Implements',
                        'Implement & Wildlife' => 'Both Implement & Wildlife',
                    ])
                    ->rules('required');
                $form->text('wildlife', __('Species'));
                $form->decimal('quantity', __('Quantity (in KGs)'))
                    ->rules('required');

                $form->text('implement', __('Implements'));
                $form->textarea('description', __('Description'))
                    ->rules('required');
                /* $form->textarea('wildlife', __('Wildlife'));
                $form->textarea('implements', __('Implements')); */

                $form->image('photos', __('Exhibit Photo'));
            });
        });


        $form->tab('Case progress comments', function (Form $form) {
            $form->morphMany('comments', 'Click on new to add a case progress comment', function (Form\NestedForm $form) {
                $u = Admin::user();
                $form->hidden('comment_by')->default($u->id);

                $form->text('body', __('Progress comment'))->rules('required');
            });
        });



        return $form;
    }
}
