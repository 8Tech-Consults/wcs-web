<?php

namespace App\Admin\Controllers;

use App\Models\CaseModel;
use App\Models\CaseSuspect;
use App\Models\Location;
use App\Models\Offence;
use App\Models\PA;
use App\Models\Utils;
use Dflydev\DotAccessData\Util;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Auth;

class CaseSuspectController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Suspects';

    /** 
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $statuses = [1, 2, 3];

        $seg = "";
        $segments = request()->segments();
        if (in_array('case-suspects', $segments)) {
            $seg = "suspects";
        } else {
        }

        $pendingCase = Utils::hasPendingCase(Auth::user());
        if ($pendingCase != null) {
            if ($pendingCase->case_step == 1) {
                return redirect(admin_url("new-case-suspects/{$pendingCase->id}/edit"));
            }
            return redirect(admin_url("new-case-suspects/{$pendingCase->id}/edit"));
            //dd($pendingCase);
        }



        $grid = new Grid(new CaseSuspect());


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





        $grid->model()
            ->orderBy('id', 'Desc');



        $grid->export(function ($export) {

            $export->except(['actions']);

            // $export->only(['column3', 'column4' ...]);


            $export->filename('Suspects.csv');

            $export->except(['photo_url', 'action']);
            // $export->originalValue(['is_jailed']);

            $export->column('is_jailed', function ($value, $original) {
                if ($original) {
                    return 'Jailed';
                } else {
                    return 'Not jailed';
                }
            });

            $export->column('national_id_number', function ($value, $original) {
                return 'CM' . $original;
            });

            // $export->only(['column3', 'column4' ...]);

            /*  
            $export->column('status', function ($value, $original) {

                if ($value == 0) {
                    return 'Pending';
                } else if ($value == 1) {
                    return 'Active';
                } {
                }
                return 'Closed';
            }); */
        });



        $grid->disableBatchActions();
        $grid->disableActions();
        $grid->disableCreateButton();






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


            $f->equal('is_jailed', 'Filter by jail status')->select([
                0 => 'Not jailed',
                1 => 'Jailed',
            ]);
        });




        $grid->quickSearch('first_name')->placeholder('Search by first name..');

        $grid->column('case_id', __('Case number'))
            ->display(function ($x) {
                return $this->case->case_number;
            });

        $grid->column('id', __('Suspect number'))
            ->display(function () {
                return $this->suspect_number;
            })
            ->sortable();


        $grid->column('created_at', __('Date'))
            ->display(function ($x) {
                return Utils::my_date_time($x);
            })
            ->sortable();



        $grid->column('case_num', __('Case title'))
            ->display(function ($x) {
                return $this->case->title;
            })
            ->hide();

        $grid->column('officer', __('Officer'))
            ->display(function ($x) {
                return $this->case->officer_in_charge;
            })->hide();

        $grid->column('is_offence_committed_in_pa', __('In P.A'))
            ->display(function ($x) {
                if ($this->case->is_offence_committed_in_pa == 1) {
                    return 'Yes';
                } else {
                    return 'No';
                }
            })->hide();

        $grid->column('pa_id', __('P.A'))
            ->display(function ($x) {

                if ($this->case->is_offence_committed_in_pa != 1) {
                    return "-";
                }
                if ($this->case->pa == null) {
                    return  '-';
                }
                return $this->case->pa->name;
            })->hide();

        $grid->column('ca_id', __('C.A'))
            ->display(function ($x) {
                if ($this->case->ca == null) {
                    return  '-';
                }
                return $this->case->ca->name;
            })->hide();

        $grid->column('gps', __('GPS'))
            ->display(function ($x) {
                return $this->case->latitude . "," . $this->case->longitude;
            })->hide();

        $grid->column('detection_method', __('Detection method'))
            ->display(function ($x) {
                return $this->case->detection_method;
            })->hide();

        $grid->column('photo_url', __('Photo'))
            ->width(60)
            ->lightbox(['width' => 60, 'height' => 80]);
        /*      $grid->column('updated_at', __('Updated'))
            ->display(function ($x) {
                return Utils::my_date_time($x);
            })
            ->sortable()->hide(); */



        $grid->column('first_name', __('Suspect\'s Name'))
            ->display(function ($x) {
                return $this->first_name . " " . $this->middle_name . " " . $this->last_name;
            })
            ->sortable();



        $grid->column('sex', __('Sex'))
            ->hide()
            ->filter([
                'Male' => 'Male',
                'Female' => 'Female',
            ])
            ->sortable();

        $grid->column('age', __('D.O.B'))
            ->display(function ($x) {
                return Utils::my_date($x);
            })
            ->hide()
            ->sortable();
        $grid->column('phone_number', __('Phone number'))->hide();
        $grid->column('national_id_number', __('NIN'))->hide();
        $grid->column('ca_id', __('CA'))
            ->display(function () {
                if ($this->ca == null) {
                    return  "-";
                }
                return $this->ca->name;
            })
            ->sortable();
        $grid->column('occuptaion', __('Occupation'))->hide();
        $grid->column('country', __('Country'))->sortable();
        $grid->column('district_id', __('District'))->display(function () {
            if ($this->country != 'Uganda') {
                return '-';
            }
            return $this->district->name;
        })->sortable();


        $grid->column('parish')->display(function ($x) {
            if ($this->country != 'Uganda') {
                return '-';
            }
            return $x;
        })->hide()->sortable();
        $grid->column('village')->display(function ($x) {
            if ($this->country != 'Uganda') {
                return '-';
            }
            return $x;
        })->hide()->sortable();

        $grid->column('ethnicity')->display(function ($x) {
            if ($this->country != 'Uganda') {
                return '-';
            }
            return $x;
        })->hide()->sortable();

        $grid->column('offences_text', 'Offences');

        $grid->column('is_suspects_arrested', 'Is arrested')
        ->using([
            1 => 'Arrested',
            0 => 'Not arrested',
        ], 'Not arrested')
        ->sortable();

        $grid->column('arrest_date_time', 'Arrest date')
        ->hide()
        ->display(function ($d) {
            return Utils::my_date($d);
        }); 
 

        $grid->column('arrest_in_pa', __('Arrest in P.A'))
            ->display(function ($x) {
                if ($x == 'Yes') {
                    return 'Yes';
                }
                return 'No';
            })
            ->hide()
            ->sortable();

        $grid->column('pa_id', 'P.A of Arrest ')
            ->display(function ($x) {
                if ($this->arrest_in_pa != 'Yes') {
                    return '-';
                }
                if ($this->arrestPa == null) {
                    return '-';
                }
                return $this->arrestPa->name;
            })
            ->sortable()
            ->hide();
        $grid->column('ca_id', 'Nearest C.A')
            ->display(function ($x) {
                if ($this->arrestCa == null) {
                    return '-';
                }
                return $this->arrestCa->name;
            })
            ->sortable() 
            ->hide();
            
        $grid->column('arrest_district_id', __('District'))
        ->display(function ($x) {
            return Utils::get('App\Models\Location', $this->arrest_district_id)->name_text;
        })
        ->hide()
        ->sortable();

        $grid->column('arrest_sub_county_id', __('Sub-county'))
            ->display(function ($x) {
                return Utils::get(Location::class, $this->arrest_sub_county_id)->name;
            })
            ->hide()
            ->sortable();

        $grid->column('arrest_parish')->hide()->sortable();
        $grid->column('arrest_village')->hide()->sortable();
        $grid->column('arrest_latitude', 'Arrest GPS latitude')->hide()->sortable();
        $grid->column('arrest_longitude', 'Arrest GPS longitude')->hide()->sortable();
        $grid->column('arrest_first_police_station', 'First police station')->hide()->sortable();
        $grid->column('arrest_current_police_station', 'Current police station')->hide()->sortable();
        $grid->column('arrest_agency', 'Arrest agency')->hide()->sortable();
        $grid->column('arrest_uwa_unit')->hide()->sortable();
        $grid->column('arrest_crb_number')->hide()->sortable();


        $grid->column('is_suspect_appear_in_court', __('In court'))
            ->display(function ($x) {
                if ($x) {
                    return 'In court';
                } else {
                    return 'Not in court';
                }
            })
            ->hide()
            ->sortable();
        $grid->column('court_date', 'Court date')
            ->hide()
            ->display(function ($d) {
                return Utils::my_date($d);
            });

        $grid->column('prosecutor')->hide()->sortable();

        $grid->column('is_convicted', __('Is convicted'))
            ->display(function ($x) {
                if ($x) {
                    return 'Convicted';
                } else {
                    return 'Not convicted';
                }
            })
            ->hide()
            ->sortable();

        $grid->column('case_outcome', 'Court outcome')->hide()->sortable();
        $grid->column('magistrate_name')->hide()->sortable();
        $grid->column('court_name')->hide()->sortable();
        $grid->column('court_file_number')->hide()->sortable();


        $grid->column('is_jailed', __('Jailed'))

            ->display(function ($is_jailed) {
                if ($is_jailed) {
                    return 'Jailed';
                } else {
                    return 'Not jailed';
                }
            })
            ->dot([
                null => 'danger',
                1 => 'danger',
                0 => 'success',
            ], 'danger')
            ->filter([
                1 => 'Jailed',
                0 => 'Not Jailed',
            ]);

        $grid->column('jail_date', 'Arrest date')
            ->hide()
            ->display(function ($d) {
                return Utils::my_date($d);
            });

        $grid->column('jail_period')->hide()->sortable();
        $grid->column('is_fined', 'Is fined')
            ->using([
                1 => 'Fined',
                0 => 'Not fined',
            ])
            ->hide()
            ->sortable();
        $grid->column('fined_amount')->hide()->sortable();
        $grid->column('management_action')->hide()->sortable();
        $grid->column('community_service')->hide()->sortable();

        $grid->column('reported_by', __('Reported by'))
            ->display(function () {

                return $this->case->reportor->name;
            })->hide()
            ->sortable();
        $grid->actions(function ($actions) {
            if (
                (!Auth::user()->isRole('admin'))
            ) {
                $actions->disableEdit();
                $actions->disableDelete();
            }
        });


        $grid->column('action', __('Actions'))->display(function () {

            $view_link = '<a class="" href="' . url("case-suspects/{$this->id}") . '">
            <i class="fa fa-eye"></i>View</a>';
            $edit_link = "";
            if (
                !Auth::user()->isRole('ca-agent') ||
                !Auth::user()->isRole('ca-manager') ||
                !Auth::user()->isRole('hq-team-leaders') ||
                !Auth::user()->isRole('ca-team')

            ) {
                $edit_link = '<br> <a class="" href="' . url("case-suspects/{$this->id}/edit") . '"> 
            <i class="fa fa-edit"></i> Edit</a>';
            }
            return $view_link . $edit_link;
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

        $s = CaseSuspect::findOrFail($id);
        return view('admin.case-suspect-details', [
            's' => $s
        ]);

        $show = new Show(CaseSuspect::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('case_id', __('Case id'));
        $show->field('uwa_suspect_number', __('Uwa suspect number'));
        $show->field('first_name', __('First name'));
        $show->field('middle_name', __('Middle name'));
        $show->field('last_name', __('Last name'));
        $show->field('phone_number', __('Phone number'));
        $show->field('national_id_number', __('National id number'));
        $show->field('sex', __('Sex'));
        $show->field('age', __('Age'));
        $show->field('occuptaion', __('Occupation'));
        $show->field('country', __('Country'));
        $show->field('district_id', __('District id'));
        $show->field('sub_county_id', __('Sub county id'));
        $show->field('parish', __('Parish'));
        $show->field('village', __('Village'));
        $show->field('ethnicity', __('Ethnicity'));
        $show->field('finger_prints', __('Finger prints'));
        $show->field('is_suspects_arrested', __('Is suspects arrested'));
        $show->field('arrest_date_time', __('Arrest date time'));
        $show->field('arrest_district_id', __('Arrest district id'));
        $show->field('arrest_sub_county_id', __('Sub county of Arrest'));
        $show->field('arrest_parish', __('Parish of Arrest'));
        $show->field('arrest_village', __('Arrest village'));
        $show->field('arrest_latitude', __('Arrest latitude'));
        $show->field('arrest_longitude', __('Arrest longitude'));
        $show->field('arrest_first_police_station', __('Arrest first police station'));
        $show->field('arrest_current_police_station', __('Arrest current police station'));
        $show->field('arrest_agency', __('Arresting agency'));
        $show->field('arrest_uwa_unit', __('Arrest uwa unit'));
        $show->field('arrest_detection_method', __('Arrest detection method'));
        $show->field('arrest_uwa_number', __('Arrest uwa number'));
        $show->field('arrest_crb_number', __('Arrest crb number'));
        $show->field('is_suspect_appear_in_court', __('Is suspect appear in court'));
        $show->field('prosecutor', __('Prosecutor'));
        $show->field('case_outcome', __('Case outcome'));
        $show->field('magistrate_name', __('Magistrate name'));
        $show->field('court_name', __('Court name'));
        $show->field('court_file_number', __('Court file number'));
        $show->field('is_jailed', __('Is jailed'));
        $show->field('jail_period', __('Jail period'));
        $show->field('is_fined', __('Is fined'));
        $show->field('fined_amount', __('Fined amount'));
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

        $pendingCase = Utils::hasPendingCase(Auth::user());

        if ($pendingCase != null) {
            $suspects_count = count($pendingCase->suspects);
            admin_warning("Adding suspects to new case {$pendingCase->case_number} - {$pendingCase->title}, with {$suspects_count} suspects.");
        }

        $form = new Form(new CaseSuspect());

        $form->disableCreatingCheck();
        $form->disableReset();



        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
        });



        $form->divider('Case');



        $ajax_url = url(
            '/api/ajax?'
                . "&search_by_1=title"
                . "&search_by_2=id"
                . "&model=CaseModel"
        );

        if ($form->isCreating() || $form->isEditing()) {

            $pendingCase = Utils::hasPendingCase(Auth::user());
            $case_id = 0;
            if ($pendingCase != null) {
                $case_id = $pendingCase->id;
            }
            if (isset($_GET['case_id'])) {
                $case_id = ((int)($_GET['case_id']));
            }
            $form->select('case_id', 'Case')->options(function ($id) {

                if (isset($_GET['case_id'])) {
                    $id = ((int)($_GET['case_id']));
                }

                $a = CaseModel::find($id);
                if ($a) {
                    return [$a->id => "" . $a->case_number . " - " . $a->title];
                }
            })
                ->readOnly()
                ->default($case_id)
                ->rules('required')
                ->ajax($ajax_url);
        } else {

            $form->select('case_id', 'Select case')->options(function ($id) {
                $a = CaseModel::find($id);
                if ($a) {
                    return [$a->id => "#" . $a->id . " - " . $a->title];
                }
            })
                ->readOnly()
                ->rules('required')
                ->ajax($ajax_url);
        }


        $form->divider('Suspect Bio data');



        $form->image('photo', 'Suspect photo');



        $form->text('first_name')->rules('required');
        $form->text('middle_name');
        $form->text('last_name')->rules('required');
        $form->radio('sex')->options([
            'Male' => 'Male',
            'Female' => 'Female',
        ])->rules('required');
        $form->date('age', 'Date of birth')->rules('required');
        $form->mobile('phone_number')->options(['mask' => '999 9999 9999']);
        $form->text('national_id_number');
        $form->text('occuptaion', 'Occupation');


        $form->radio('is_ugandan', __('Is the suspect a Ugandan'))
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
                    ->help('Where this suspect originally lives')
                    ->options(Location::get_sub_counties_array());
                $form->select('sub_county_id', __('Sub county'))
                    ->rules('required')
                    ->help('Where this suspect originally lives')
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



        /* 
        $form->morphMany('offences', 'Click on new to add offence', function (Form\NestedForm $form) {
            $offences = Offence::all()->pluck('name', 'id');
            $form->select('offence_id', 'Select offence')->rules('required')->options($offences);
            $form->text('vadict', __('Vadict'));
        }); */


        $form->radio('is_suspects_arrested', "Is this suspect arrested?")
            ->options([
                1 => 'Yes',
                0 => 'No',
            ])
            ->rules('required')
            ->when(0, function ($form) {
                $form->select('management_action', 'Action taken by management')->options([
                    'Fined' => 'Fined',
                    'Cautioned' => 'Cautioned',
                ]);

                $form->textarea('not_arrested_remarks', 'Remarks');
            })
            ->when(1, function ($form) {

                $form->divider('Arrest information');
                $form->datetime('arrest_date_time', 'Arrest date and time');

                $form->radio('arrest_in_pa', "Was suspect arrested within a P.A")
                    ->options([
                        'Yes' => 'Yes',
                        'No' => 'No',
                    ])
                    ->when('Yes', function ($form) {
                        $form->select('pa_id', __('Select PA'))
                            ->options(PA::all()->pluck('name_text', 'id'));
                    })
                    ->when('No', function ($form) {
                        $form->select('arrest_sub_county_id', __('Sub county of Arrest'))
                            ->rules('int|required')
                            ->help('Where this suspect was arrested')
                            ->options(Location::get_sub_counties_array());


                        $form->text('arrest_parish', 'Parish of Arrest');
                        $form->text('arrest_village', 'Arrest village');
                    })
                    ->rules('required');


                $form->text('arrest_latitude', 'Arrest GPS - latitude')->help('e.g  41.40338');
                $form->text('arrest_longitude', 'Arrest GPS - longitude')->help('e.g  2.17403');

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
                ]);
                $form->select('arrest_uwa_unit', 'UWA Unit')->options([
                    'Canine Unit' => 'The Canine Unit',
                    'WCU' => 'WCU',
                    'NRCN' => 'NRCN',
                    'LEU' => 'LEU',
                ]);

                $form->text('arrest_crb_number', 'Police CRB number');
                $form->text('police_sd_number', 'Police SD number');


                $form->radio('is_suspect_appear_in_court', __('Has this suspect appeared in court?'))
                    ->options([
                        1 => 'Yes',
                        0 => 'No',
                    ])
                    ->when(0, function ($form) {
                        $form->radio('status', __('Case status'))
                            ->options([
                                1 => 'On-going investigation',
                                2 => 'Closed',
                                3 => 'Re-opened',
                            ])
                            ->rules('required')
                            ->when(1, function ($form) {
                                $form->select('police_action', 'Case outcome at police level')->options([
                                    'Police bond' => 'Police bond',
                                    'Skipped bond' => 'Skipped bond',
                                ]);
                            })
                            ->when(2, function ($form) {
                                $form->select('police_action', 'Case outcome at police level')->options([
                                    'Dismissed by state' => 'Dismissed by state',
                                    'Withdrawn by complainant' => 'Withdrawn by complainant',
                                ]);
                                $form->date('police_action_date', 'Date');
                                $form->textarea('police_action_remarks', 'Remarks');
                            })->when(3, function ($form) {
                                $form->select('police_action', 'Case outcome at police level')->options([
                                    'Police bond' => 'Police bond',
                                    'Skipped bond' => 'Skipped bond',
                                ]);
                                $form->date('police_action_date', 'Date');
                                $form->textarea('police_action_remarks', 'Remarks');
                            });
                    })
                    ->when(1, function ($form) {

                        $form->divider('Court information');
                        $form->text('court_file_number', 'Court file number');
                        $form->date('court_date', 'Court date');
                        $form->text('court_name', 'Court Name');

                        $form->text('prosecutor', 'Lead prosecutor');
                        $form->text('magistrate_name', 'Magistrate Name');

                        $form->radio('court_status', __('Court case status'))
                            ->options([
                                'On-going investigation' => 'On-going investigation',
                                'On-going prosecution' => 'On-going prosecution',
                                'Closed' => 'Closed',
                            ])->when('Closed', function ($form) {

                                $form->radio('case_outcome', 'Specific court case status')->options([
                                    'Dismissed' => 'Dismissed',
                                    'Convicted' => 'Convicted',
                                ])
                                    ->when('Convicted', function ($form) {
                                        $form->radio('is_jailed', __('Was suspect jailed?'))
                                            ->options([
                                                1 => 'Yes',
                                                0 => 'No',
                                            ])
                                            ->when(1, function ($form) {
                                                $form->date('jail_date', 'Jail date');
                                                $form->decimal('jail_period', 'Jail period')->help("(In months)");
                                                $form->text('prison', 'Prison name');
                                                $form->date('jail_release_date', 'Date released');
                                            });

                                        $form->radio('is_fined', __('Was suspect fined?'))
                                            ->options([
                                                1 => 'Yes',
                                                0 => 'No',
                                            ])
                                            ->when(1, function ($form) {
                                                $form->decimal('fined_amount', 'Fine amount')->help("(In UGX)");
                                            });

                                        $form->radio('community_service', __('Was suspected issued a community service?'))
                                            ->options([
                                                'Yes' => 'Yes',
                                                'No' => 'No',
                                            ])
                                            ->when(1, function ($form) {
                                                $form->date('created_at', 'Court date');
                                            });

                                        $form->radio('suspect_appealed', __('Did the suspect appeal?'))
                                            ->options([
                                                'Yes' => 'Yes',
                                                'No' => 'No',
                                            ])
                                            ->when('Yes', function ($form) {
                                                $form->date('suspect_appealed_date', 'Suspect appeal date');
                                                $form->text('suspect_appealed_court_name', 'Court of appeal');
                                                $form->text('suspect_appealed_court_file', 'Appeal court file number');
                                            });
                                    });
                            })
                            ->when('in', ['On-going investigation', 'On-going prosecution'], function ($form) {


                                $form->radio('suspect_court_outcome', 'Suspect court case status')->options([
                                    'Remand' => 'Remand',
                                    'Court Bail' => 'Court bail',
                                ]);

                                $form->radio('court_file_status', 'Court file status')->options([
                                    'Perusal' => 'Perusal',
                                    'Further investigation' => 'Further investigation',
                                ]);
                            });
                    });
            });


        return $form;
    }
}
