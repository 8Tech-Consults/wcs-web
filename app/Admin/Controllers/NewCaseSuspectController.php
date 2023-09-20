<?php

namespace App\Admin\Controllers;

use App\Models\ArrestingAgency;
use App\Models\CaseModel;
use App\Models\CaseSuspect;
use App\Models\ConservationArea;
use App\Models\Court;
use App\Models\Location;
use App\Models\Offence;
use App\Models\PA;
use App\Models\SuspectCourtStatus;
use App\Models\User;
use App\Models\Utils;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Auth;
use App\Rules\AfterDateInDatabase;

class NewCaseSuspectController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Adding new suspect to case';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $pendingCase = Utils::hasPendingCase(Auth::user());

        if (isset($_GET['cancel_add_suspect'])) {
            $c = CaseModel::find($_GET['cancel_add_suspect']);
            if ($c != null) {
                $c->user_adding_suspect_id = null;
                $c->save();

                Admin::script('window.location.replace("' . admin_url("cases") . '");');
                return 'Loading...';
            }
        }

        if ($pendingCase != null) {
            if ($pendingCase->case_step == 1) {
                Admin::script('window.location.replace("' . admin_url("new-case-suspects/create") . '");');
                return 'Loading...';
            } else if ($pendingCase->case_step == 2) {
                Admin::script('window.location.replace("' . admin_url("new-exhibits-case-models/create") . '");');
                return 'Loading...';
            } else if ($pendingCase->case_step == 3) {
            } else {
            }
            Admin::script('window.location.replace("' . admin_url("new-confirm-case-models/{$pendingCase->id}/edit") . '");');
            return 'Loading...';
        }

        Admin::script('window.location.replace("' . admin_url("cases") . '");');
        return 'Loading...';


        $grid = new Grid(new CaseSuspect());

        $grid->column('id', __('Id'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        $grid->column('case_id', __('Case id'));
        $grid->column('uwa_suspect_number', __('Uwa suspect number'));
        $grid->column('first_name', __('First name'));
        $grid->column('middle_name', __('Middle name'));
        $grid->column('last_name', __('Last name'));
        $grid->column('phone_number', __('Phone number'));
        $grid->column('national_id_number', __('National id number'));
        $grid->column('sex', __('Sex'));
        $grid->column('age', __('Age'));
        $grid->column('occuptaion', __('Occupation'));
        $grid->column('country', __('Country'));
        $grid->column('district_id', __('District id'));
        $grid->column('sub_county_id', __('Sub county id'));
        $grid->column('parish', __('Parish'));
        $grid->column('village', __('Village'));
        $grid->column('ethnicity', __('Ethnicity'));
        $grid->column('finger_prints', __('Finger prints'));
        $grid->column('is_suspects_arrested', __('Is suspects arrested'));
        $grid->column('arrest_date_time', __('Arrest date time'));
        $grid->column('arrest_district_id', __('Arrest district id'));
        $grid->column('arrest_sub_county_id', __('Arrest sub county id'));
        $grid->column('arrest_parish', __('Arrest parish'));
        $grid->column('arrest_village', __('Arrest village'));
        $grid->column('arrest_latitude', __('Arrest latitude'));
        $grid->column('arrest_longitude', __('Arrest longitude'));
        $grid->column('arrest_first_police_station', __('Arrest first police station'));
        $grid->column('arrest_current_police_station', __('Arrest current police station'));
        $grid->column('arrest_agency', __('Lead Arrest agency'));
        $grid->column('arrest_uwa_unit', __('Arrest uwa unit'));
        $grid->column('arrest_detection_method', __('Arrest detection method'));
        $grid->column('arrest_uwa_number', __('Arrest uwa number'));
        $grid->column('arrest_crb_number', __('Arrest crb number'));
        $grid->column('is_suspect_appear_in_court', __('Is suspect appear in court'));
        $grid->column('prosecutor', __('Prosecutor'));
        $grid->column('is_convicted', __('Is convicted'));
        $grid->column('case_outcome', __('Case outcome'));
        $grid->column('magistrate_name', __('Magistrate name'));
        $grid->column('court_name', __('Court name'));
        $grid->column('court_file_number', __('Court file number'));
        $grid->column('is_jailed', __('Is jailed'));
        $grid->column('jail_period', __('Jail period'));
        $grid->column('is_fined', __('Is fined'));
        $grid->column('fined_amount', __('Fined amount'));
        $grid->column('status', __('Status'));
        $grid->column('deleted_at', __('Deleted at'));
        $grid->column('photo', __('Photo'));
        $grid->column('court_date', __('Court date'));
        $grid->column('jail_date', __('Jail date'));
        $grid->column('use_same_arrest_information', __('Use same arrest information'));
        $grid->column('suspect_number', __('Suspect number'));
        $grid->column('arrest_in_pa', __('Arrest in pa'));
        $grid->column('pa_id', __('Pa id'));
        // $grid->column('arrest_location', __('Arrest location'));
        $grid->column('management_action', __('Management action'));
        $grid->column('community_service', __('Community service'));
        $grid->column('reported_by', __('Reported by'));

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

        $pendingCase = Utils::hasPendingCase(Auth::user());
        if ($pendingCase != null) {
            if ($pendingCase->case_step == 1) {
                Admin::script('window.location.replace("' . admin_url("new-case-suspects/create") . '");');
                return 'Loading...';
            } else if ($pendingCase->case_step == 2) {
                Admin::script('window.location.replace("' . admin_url("new-exhibits-case-models/create") . '");');
                return 'Loading...';
            } else if ($pendingCase->case_step == 3) {
            } else {
            }
            Admin::script('window.location.replace("' . admin_url("new-confirm-case-models/{$pendingCase->id}/edit") . '");');
            return 'Loading...';
        }
        Admin::script('window.location.replace("' . admin_url("new-confirm-case-models/{$pendingCase->id}/edit") . '");');
        return 'Loading...';
        //dd($pendingCase);
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
        $show->field('arrest_sub_county_id', __('Arrest sub county id'));
        $show->field('arrest_parish', __('Arrest parish'));
        $show->field('arrest_village', __('Arrest village'));
        // $show->field('arrest_location', __('Arrest location'));
        $show->field('arrest_latitude', __('Arrest latitude'));
        $show->field('arrest_longitude', __('Arrest longitude'));
        $show->field('arrest_first_police_station', __('Arrest first police station'));
        $show->field('arrest_current_police_station', __('Arrest current police station'));
        $show->field('arrest_agency', __('Lead Arrest agency'));
        $show->field('arrest_uwa_unit', __('Arrest uwa unit'));
        $show->field('arrest_detection_method', __('Arrest detection method'));
        $show->field('arrest_uwa_number', __('Arrest uwa number'));
        $show->field('arrest_crb_number', __('Arrest crb number'));
        $show->field('is_suspect_appear_in_court', __('Is suspect appear in court'));
        $show->field('prosecutor', __('Prosecutor'));
        $show->field('is_convicted', __('Is convicted'));
        $show->field('case_outcome', __('Case outcome'));
        $show->field('magistrate_name', __('Magistrate name'));
        $show->field('court_name', __('Court name'));
        $show->field('court_file_number', __('Court file number'));
        $show->field('is_jailed', __('Is jailed'));
        $show->field('jail_period', __('Jail period'));
        $show->field('is_fined', __('Is fined'));
        $show->field('fined_amount', __('Fined amount'));
        $show->field('status', __('Status'));
        $show->field('deleted_at', __('Deleted at'));
        $show->field('photo', __('Photo'));
        $show->field('court_date', __('Court date'));
        $show->field('jail_date', __('Jail date'));
        $show->field('use_same_arrest_information', __('Use same arrest information'));
        $show->field('suspect_number', __('Suspect number'));
        $show->field('arrest_in_pa', __('Arrest in pa'));
        $show->field('pa_id', __('Pa id'));
        $show->field('management_action', __('Management action'));
        $show->field('community_service', __('Community service'));
        $show->field('reported_by', __('Reported by'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {

        $form = new Form(new CaseSuspect());

        $form->saved(function (Form $form) {
            $pendingCase = Utils::hasPendingCase(Auth::user());
            if ($pendingCase != null) {
                if ($pendingCase->case_step == 1) {
                    Admin::script('window.location.replace("' . admin_url("new-case-suspects/create") . '");');
                } else if ($pendingCase->case_step == 2) {
                    Admin::script('window.location.replace("' . admin_url("new-exhibits-case-models/create") . '");');
                } else if ($pendingCase->case_step == 3) {
                } else {
                }

                Admin::script('window.location.replace("' . admin_url("new-confirm-case-models/{$pendingCase->id}/edit") . '");');
            }
        });

        $pendingCase = Utils::hasPendingCase(Auth::user());
        if ($pendingCase == null) {
            die("active case not found.");
        } else {
            $form->hidden('case_id', 'Suspect photo')->default($pendingCase->id)->value($pendingCase->id);
        }

        if ($pendingCase->user_adding_suspect_id != Auth::user()->id) {
            Admin::css(url('/css/new-case.css'));
            $form->html(view('steps', ['active' => 2, 'case' => $pendingCase]));


            if (count($pendingCase->suspects) > 0) {
                $form->html('<a class="btn btn-danger" href="' . admin_url("new-exhibits-case-models/create") . '" >SKIP TO EXHIBITS</a>', 'SKIP');
            }
        } else {
            $form->html('<p style="padding: 0;margin: 0;"><a href="/new-case-suspects?cancel_add_suspect=' . $pendingCase->id . '" class="text-danger"><b>Cancel add suspect process</b></a></p>');
            $form->display('ADDING SUSPECT TO CASE')->value($pendingCase->case_number)->default($pendingCase->case_number);
        }

        $form->disableCreatingCheck();
        $form->disableReset();
        $form->disableEditingCheck();
        $form->disableViewCheck();





        $form->divider('Suspect Bio data');


        $form->image('photo', 'Suspect photo');



        $form->text('first_name')->rules('required');
        $form->text('middle_name');
        $form->text('last_name')->rules('required');
        $form->radio('sex')->options([
            'Male' => 'Male',
            'Female' => 'Female',
        ])->rules('required');
        $form->text('age', 'Suspect\'s Age')->help("How old is the suspect?")->rules('nullable|int|min:1|max:200');
        $form->text('phone_number', 'Phone number');

        $form->radio('type_of_id', 'Suspect Type of Identification Card')
            ->options([
                'National ID' => 'National ID',
                'Passport ' => 'Passport',
                'Driving license' => 'Driving license',
                'School ID Card' => 'School ID Card',
                'Employee ID Card' => 'Employee ID Card',
                'Other' => 'Other',
            ]);
        $form->text('national_id_number', 'Suspect Identification Number');
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


        $hasPendingSusps = false;
        $pendingCase = Utils::hasPendingCase(Auth::user());
        if ($pendingCase != null) {
            if ($pendingCase->suspects()->count() > 0) {
                $hasPendingSusps = true;
            }
        }


        if (!$hasPendingSusps) {
            $form->listbox('offences', 'Offences')->options(Offence::all()->pluck('name', 'id'))
                ->help("Select offences involded in this case")
                ->rules('required');
        } else {
            $form->radio('use_offence', "Do you want to use existing offence information for this suspect?")
                ->options([
                    'No' => 'No',
                    'Yes' => 'Yes',
                ])->when('No', function ($form) {
                    $form->listbox('offences', 'Offences')->options(Offence::all()->pluck('name', 'id'))
                        ->help("Select offences involded in this case")
                        ->rules('required');
                })
                ->when('Yes', function ($form) {
                    $supects = [];
                    $pendingCase = Utils::hasPendingCase(Auth::user());
                    if ($pendingCase != null) {
                        if ($pendingCase->suspects()->count() > 0) {
                            foreach ($pendingCase->suspects as $sus) {
                                $supects[$sus->id] = $sus->uwa_suspect_number . " - " . $sus->name;
                            }
                        }
                    }
                    $form->select('use_offence_suspect_id', 'Select suspect')
                        ->options($supects)
                        ->rules('required');
                })
                ->rules('required');
        }


        $form->radio('is_suspects_arrested', "Has suspect been handed over to police?")
            ->options([
                'Yes' => 'Yes',
                'No' => 'No',
            ])
            ->when('No', function ($form) {
                $form->select('management_action', 'Action taken by management')->options([
                    'Fined' => 'Fined',
                    'Cautioned and Released' => 'Cautioned and Released',
                    'At Large' => 'At Large',
                ])->rules('required');

                $form->textarea('not_arrested_remarks', 'Remarks');
            })
            ->when('Yes', function ($form) {


                $form->divider('Arrest information');

                $hasPendingSusps = false;
                $csb = null;
                $pendingCase = Utils::hasPendingCase(Auth::user());
                if ($pendingCase != null) {
                    if ($pendingCase->suspects->count() > 0) {
                        $hasPendingSusps = true;
                        $csb = $pendingCase->getCrbNumber();
                    }
                }


                if ($hasPendingSusps) {
                    $form->radio('use_same_arrest_information', "Do you want to use existing arrest information for this suspect?")
                        ->options([
                            'No' => 'No',
                            'Yes' => 'Yes',
                        ])->when('No', function ($form) {
                            $hasPendingSusps = false;
                            $csb = null;
                            $sd = null;
                            $pendingCase = Utils::hasPendingCase(Auth::user());
                            if ($pendingCase != null) {
                                if ($pendingCase->suspects->count() > 0) {
                                    $hasPendingSusps = true;
                                    $sd = $pendingCase->getSdNumber();
                                    $csb = $pendingCase->getCrbNumber();
                                }
                            }

                            $form->date('arrest_date_time', 'Arrest date and time')
                                ->rules(['required', new AfterDateInDatabase('case_models', $pendingCase->id, 'case_date')]);

                            $form->radio('arrest_in_pa', "Was suspect arrested within a P.A")
                                ->options([
                                    'Yes' => 'Yes',
                                    'No' => 'No',
                                ])
                                ->when('Yes', function ($form) {
                                    $form->select('pa_id', __('Select PA'))
                                        ->rules('required')
                                        ->options(PA::where('id', '!=', 1)->get()
                                            ->pluck('name_text', 'id'));
                                    $form->text('arrest_village', 'Enter arrest location');
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
                            $form->select('arrest_agency', 'Lead Arresting agency')->options(
                                ArrestingAgency::orderBy('name','Desc')->pluck('name', 'name')
                            )
                                ->when('UWA', function ($form) {
                                    $form->select('arrest_uwa_unit', 'UWA Unit')->options([
                                        'Canine Unit' => 'The Canine Unit',
                                        'WCU' => 'WCU',
                                        'LEU' => 'LEU',
                                    ]);
                                });
                            $form->multipleSelect('other_arrest_agencies', 'Other arresting agencies')->options(
                                ArrestingAgency::orderBy('name','Desc')->pluck('name', 'name')
                            );

                            if ($csb == null) {
                                $form->text('arrest_crb_number', 'Police CRB number');
                            } else {
                                $form->text('arrest_crb_number', 'Police CRB number')
                                    ->rules('required')
                                    ->default($csb)
                                    ->value($csb)
                                    ->readonly();
                            }

                            if ($sd == null) {
                                $form->text('police_sd_number', 'Police SD number');
                            } else {
                                $form->text('police_sd_number', 'Police SD number')
                                    ->default($sd)
                                    ->value($sd)
                                    ->readonly();
                            }
                        })
                        ->when('Yes', function ($form) {
                            $supects = [];
                            $pendingCase = Utils::hasPendingCase(Auth::user());
                            if ($pendingCase != null) {
                                if ($pendingCase->suspects->count() > 0) {
                                    foreach ($pendingCase->suspects as $sus) {
                                        if ($sus->arrest_date_time == null) {
                                            continue;
                                        }
                                        if (strlen($sus->arrest_date_time) < 4) {
                                            continue;
                                        }
                                        $supects[$sus->id] = $sus->uwa_suspect_number . " - " . $sus->name;
                                    }
                                }
                            }
                            $form->select('use_same_arrest_information_id', 'Select suspect')
                                ->options($supects)
                                ->rules('required');
                        })
                        ->default('No')
                        ->rules('required');
                } else {


                    $form->date('arrest_date_time', 'Arrest date and time')
                        ->rules(['required', new AfterDateInDatabase('case_models', $pendingCase->id, 'case_date')]);

                    $form->radio('arrest_in_pa', "Was suspect arrested within a P.A")
                        ->options([
                            'Yes' => 'Yes',
                            'No' => 'No',
                        ])
                        ->when('Yes', function ($form) {
                            $form->select('pa_id', __('Select PA'))
                                ->rules('required')
                                ->options(PA::where('id', '!=', 1)->get()->pluck('name_text', 'id'));
                            $form->text('arrest_village', 'Enter arrest location');
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
                    $form->select('arrest_agency', 'Lead Arresting agency')->options(
                        ArrestingAgency::orderBy('name','Desc')->pluck('name', 'name')
                    )
                        ->when('UWA', function ($form) {
                            $form->select('arrest_uwa_unit', 'UWA Unit')->options([
                                'Canine Unit' => 'The Canine Unit',
                                'WCU' => 'WCU',
                                'LEU' => 'LEU',
                            ]);
                        });
                    $form->multipleSelect('other_arrest_agencies', 'Other arresting agencies')->options(
                        ArrestingAgency::orderBy('name','Desc')->pluck('name','name')
                    );


                    $hasPendingSusps = false;
                    $csb = null;
                    $sd = null;
                    $pendingCase = Utils::hasPendingCase(Auth::user());
                    if ($pendingCase != null) {
                        if ($pendingCase->suspects->count() > 0) {
                            $hasPendingSusps = true;
                            $csb = $pendingCase->getCrbNumber();
                            $sd = $pendingCase->getSdNumber();
                        }
                    }

                    if ($csb == null) {
                        $form->text('arrest_crb_number', 'Police CRB number');
                    } else {
                        $form->text('arrest_crb_number', 'Police CRB number')
                            ->rules('required')
                            ->default($csb)
                            ->value($csb)
                            ->readonly();
                    }
                    if ($sd == null) {
                        $form->text('police_sd_number', 'Police SD number');
                    } else {
                        $form->text('police_sd_number', 'Police SD number')
                            ->default($sd)
                            ->value($sd)
                            ->readonly();
                    }
                }

                if ($hasPendingSusps) {

                    $form->radio('use_same_court_information', "Do you want to use existing court information for this suspect?")
                        ->rules('required')
                        ->options([
                            'No' => 'No',
                            'Yes' => 'Yes',
                        ])->when('No', function ($form) {
                            $form->radio('is_suspect_appear_in_court', __('Has this suspect appeared in court?'))
                                ->options([
                                    'Yes' => 'Yes',
                                    'No' => 'No',
                                ])
                                ->when('No', function ($form) {

                                    $form->radio('status', __('Case status'))
                                        ->options([
                                            'On-going investigation' => 'On-going investigation',
                                            'Closed' => 'Closed',
                                            'Re-opened' => 'Re-opened',
                                        ])
                                        ->when('On-going investigation', function ($form) {
                                            $form->select('police_action', 'Case outcome at police level')->options([
                                                'Police bond' => 'Police bond',
                                                'Skipped bond' => 'Skipped bond',
                                                'Under police custody' => 'Under police custody',
                                                'Escaped from colice custody' => 'Escaped from police custody',
                                            ])
                                                ->rules('required');
                                        })
                                        ->when('Closed', function ($form) {
                                            $form->select('police_action', 'Case outcome at police level')->options([
                                                'Dismissed by state' => 'Dismissed by state',
                                                'Withdrawn by complainant' => 'Withdrawn by complainant',
                                            ])
                                                ->rules('required');
                                            $form->date('police_action_date', 'Date');
                                            $form->textarea('police_action_remarks', 'Remarks');
                                        })->when('Re-opened', function ($form) {
                                            $form->select('police_action', 'Case outcome at police level')->options([
                                                'Police bond' => 'Police bond',
                                                'Skipped bond' => 'Skipped bond',
                                                'Under Police Custody' => 'Under Police Custody',
                                                'Escaped from Police Custody' => 'Escaped from Police Custody',
                                            ])
                                                ->rules("required");

                                            $form->date('police_action_date', 'Date')->rules('required');
                                            $form->textarea('police_action_remarks', 'Remarks');
                                        })
                                        ->rules('required');
                                })
                                ->when('Yes', function ($form) {

                                    $form->divider('Court information');

                                    $courtFileNumber = null;
                                    $pendingCase = Utils::hasPendingCase(Auth::user());
                                    if ($pendingCase != null) {
                                        $courtFileNumber = $pendingCase->getCourtFileNumber();
                                    }

                                    if ($courtFileNumber == null) {
                                        $form->text('court_file_number', 'Court file number')
                                            ->rules("required");
                                    } else {
                                        $form->text('court_file_number', 'Court file number')
                                            ->default($courtFileNumber)
                                            ->value($courtFileNumber)
                                            ->readonly();
                                    }


                                    $form->date('court_date', 'Court Date of first appearance')
                                        ->rules(
                                            function (Form $form) {
                                                return ['required', new AfterDateInDatabase('case_suspects',$form->model()->id , 'arrest_date_time')];
                                            });

                                    $courts =  Court::where([])->orderBy('id', 'desc')->get()->pluck('name', 'id');
                                    $form->select('court_name', 'Select Court')->options($courts)
                                        ->when(1, function ($form) {
                                            $form->text('other_court_name', 'Specify other court name')
                                                ->rules('required');
                                        })
                                        ->rules('required');
                                    $form->text('prosecutor', 'Lead prosecutor');
                                    $form->text('magistrate_name', 'Magistrate Name');


                                    $form->radio('court_status', __('Court case status'))
                                        ->options([
                                            'On-going prosecution' => 'On-going prosecution',
                                            'Reinstated' => 'Reinstated',
                                            'Concluded' => 'Concluded',
                                        ])->when('Concluded', function ($form) {

                                            $form->radio('case_outcome', 'Specific court case status')->options([
                                                'Dismissed' => 'Dismissed',
                                                'Withdrawn by DPP' => 'Withdrawn by DPP',
                                                'Acquittal' => 'Acquittal',
                                                'Convicted' => 'Convicted',
                                            ])
                                                ->when('Convicted', function ($form) {
                                                    $form->radio('is_jailed', __('Was accused jailed?'))
                                                        ->options([
                                                            'Yes' => 'Yes',
                                                            'No' => 'No',
                                                        ])
                                                        ->when('Yes', function ($form) {
                                                            $form->date('jail_date', 'Jail date')->rules('after:court_date');
                                                            $form->decimal('jail_period', 'Jail period')->help("(In months)");
                                                            $form->text('prison', 'Prison name');
                                                            $form->date('jail_release_date', 'Release Date');
                                                        })
                                                        ->default('No');

                                                    $form->radio('is_fined', __('Was accused fined?'))
                                                        ->options([
                                                            'Yes' => 'Yes',
                                                            'No' => 'No',
                                                        ])
                                                        ->when('Yes', function ($form) {
                                                            $form->decimal('fined_amount', 'Fine amount')->help("(In UGX)");
                                                        })
                                                        ->default('No');

                                                    $form->radio('community_service', __('Was the accused offered community service?'))
                                                        ->options([
                                                            'Yes' => 'Yes',
                                                            'No' => 'No',
                                                        ])
                                                        ->when('Yes', function ($form) {
                                                            $form->decimal(
                                                                'community_service_duration',
                                                                'Community service duration (in Hours)'
                                                            );
                                                        })
                                                        ->default('No');


                                                    $form->radio('cautioned', __('Was accused cautioned?'))
                                                        ->options([
                                                            'Yes' => 'Yes',
                                                            'No' => 'No',
                                                        ])
                                                        ->when('Yes', function ($form) {
                                                            $form->text('cautioned_remarks', 'Enter caution remarks');
                                                        })
                                                        ->default('No');

                                                    $form->radio('suspect_appealed', __('Did the accused appeal?'))
                                                        ->options([
                                                            'Yes' => 'Yes',
                                                            'No' => 'No',
                                                        ])
                                                        ->when('Yes', function ($form) {
                                                            $form->date('suspect_appealed_date', 'Accused appeal Date');
                                                            $form->text('suspect_appealed_court_name', 'Appellate court');
                                                            $form->text('suspect_appealed_court_file', 'Appeal court file number');
                                                            $form->radio('suspect_appealed_outcome', __('Appeal outcome'))
                                                                ->options([
                                                                    'Upheld' => 'Upheld',
                                                                    'Quashed and acquitted' => 'Quashed and acquitted',
                                                                    'Quashed and retrial ordered' => 'Quashed and retrial ordered',
                                                                    'On-going' => 'On-going',
                                                                ]);
                                                            $form->textarea('suspect_appeal_remarks', 'Remarks');
                                                        });
                                                })
                                                ->when('in', ['Dismissed', 'Withdrawn by DPP', 'Acquittal'], function ($form) {
                                                    $form->textarea('case_outcome_remarks', 'Remarks')->rules('required');
                                                });
                                        })
                                        ->when('in', ['On-going prosecution', 'Reinstated'], function ($form) {

                                            $form->select('suspect_court_outcome', 'Accused court case status')->options(SuspectCourtStatus::pluck('name', 'name'))
                                                ->rules('required');
                                        })
                                        ->rules('required');
                                })
                                ->rules('required');
                        })->when('Yes', function ($form) {
                            $supects = [];
                            $pendingCase = Utils::hasPendingCase(Auth::user());
                            if ($pendingCase != null) {
                                if ($pendingCase->suspects->count() > 0) {
                                    foreach ($pendingCase->suspects as $sus) {
                                        if ($sus->is_suspect_appear_in_court == 'Yes') {
                                            $supects[$sus->id] = $sus->uwa_suspect_number . " - " . $sus->name;
                                        }
                                    }
                                }
                            }
                            $form->select('use_same_court_information_id', 'Select suspect')
                                ->options($supects)
                                ->rules('required');
                        })
                        ->default('No');
                } else {
                    $form->radio('is_suspect_appear_in_court', __('Has this suspect appeared in court?'))
                        ->options([
                            'Yes' => 'Yes',
                            'No' => 'No',
                        ])
                        ->when('No', function ($form) {

                            $form->radio('status', __('Case status'))
                                ->options([
                                    'On-going investigation' => 'On-going investigation',
                                    'Closed' => 'Closed',
                                    'Re-opened' => 'Re-opened',
                                ])
                                ->rules('required')
                                ->when('On-going investigation', function ($form) {
                                    $form->select('police_action', 'Case outcome at police level')->options([
                                        'Police bond' => 'Police bond',
                                        'Skipped bond' => 'Skipped bond',
                                        'Under police custody' => 'Under police custody',
                                        'Escaped from colice custody' => 'Escaped from police custody',
                                    ]);
                                })
                                ->when('Closed', function ($form) {
                                    $form->select('police_action', 'Case outcome at police level')->options([
                                        'Dismissed by state' => 'Dismissed by state',
                                        'Withdrawn by complainant' => 'Withdrawn by complainant',
                                    ]);
                                    $form->date('police_action_date', 'Date')->rules('required');
                                    $form->textarea('police_action_remarks', 'Remarks');
                                })->when('Re-opened', function ($form) {
                                    $form->select('police_action', 'Case outcome at police level')->options([
                                        'Police bond' => 'Police bond',
                                        'Skipped bond' => 'Skipped bond',
                                        'Under police custody' => 'Under police custody',
                                        'Escaped from colice custody' => 'Escaped from police custody',
                                    ]);
                                    $form->date('police_action_date', 'Date')->rules('required');
                                    $form->textarea('police_action_remarks', 'Remarks');
                                });
                        })
                        ->when('Yes', function ($form) {

                            $form->divider('Court information');
                            $courtFileNumber = null;
                            $pendingCase = Utils::hasPendingCase(Auth::user());
                            if ($pendingCase != null) {
                                $courtFileNumber = $pendingCase->getCourtFileNumber();
                            }

                            if ($courtFileNumber == null) {
                                $form->text('court_file_number', 'Court file number');
                            } else {
                                $form->text('court_file_number', 'Court file number')
                                    ->default($courtFileNumber)
                                    ->value($courtFileNumber)
                                    ->readonly();
                            }

                            $form->date('court_date', 'Court Date of first appearance')
                                ->rules(
                                    function (Form $form) {
                                        return ['nullable', new AfterDateInDatabase('case_suspects',$form->model()->id , 'arrest_date_time')];
                                    });

                            $courts =  Court::where([])->orderBy('id', 'desc')->get()->pluck('name', 'id');

                            $form->select('court_name', 'Select Court')->options($courts)
                                ->when(1, function ($form) {
                                    $form->text('other_court_name', 'Specify other court name')
                                        ->rules('required');
                                })
                                ->rules('required');

                            /* 
                            $form->select('prosecutor', 'Lead prosecutor')
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
                                ))->rules('required'); */

                            $form->text('prosecutor', 'Lead prosecutor');
                            $form->text('magistrate_name', 'Magistrate Name');


                            $form->radio('court_status', __('Court case status'))
                                ->options([
                                    'On-going prosecution' => 'On-going prosecution',
                                    'Reinstated' => 'Reinstated',
                                    'Concluded' => 'Concluded',
                                ])->when('Concluded', function ($form) {

                                    $form->radio('case_outcome', 'Specific court case status')->options([
                                        'Dismissed' => 'Dismissed',
                                        'Withdrawn by DPP' => 'Withdrawn by DPP',
                                        'Acquittal' => 'Acquittal',
                                        'Convicted' => 'Convicted',
                                    ])
                                        ->when('Convicted', function ($form) {
                                            $form->radio('is_jailed', __('Was accused jailed?'))
                                                ->options([
                                                    'Yes' => 'Yes',
                                                    'No' => 'No',
                                                ])
                                                ->when('Yes', function ($form) {
                                                    $form->date('jail_date', 'Jail date')->rules('after:court_date');
                                                    $form->decimal('jail_period', 'Jail period')->help("(In months)");
                                                    $form->text('prison', 'Prison name');
                                                    $form->date('jail_release_date', 'Date released');
                                                })
                                                ->default('No');

                                            $form->radio('is_fined', __('Was accused fined?'))
                                                ->options([
                                                    'Yes' => 'Yes',
                                                    'No' => 'No',
                                                ])
                                                ->when('Yes', function ($form) {
                                                    $form->decimal('fined_amount', 'Fine amount')->help("(In UGX)");
                                                })
                                                ->default('No');

                                            $form->radio('community_service', __('Was the accused offered community service?'))
                                                ->options([
                                                    'Yes' => 'Yes',
                                                    'No' => 'No',
                                                ])
                                                ->when('Yes', function ($form) {
                                                    $form->decimal(
                                                        'community_service_duration',
                                                        'Community service duration (in Hours)'
                                                    );
                                                })
                                                ->default('No');


                                            $form->radio('cautioned', __('Was accused cautioned?'))
                                                ->options([
                                                    'Yes' => 'Yes',
                                                    'No' => 'No',
                                                ])
                                                ->when('Yes', function ($form) {
                                                    $form->text('cautioned_remarks', 'Enter caution remarks');
                                                })
                                                ->default('No');

                                            $form->radio('suspect_appealed', __('Did the accused appeal?'))
                                                ->options([
                                                    'Yes' => 'Yes',
                                                    'No' => 'No',
                                                ])
                                                ->when('Yes', function ($form) {
                                                    $form->date('suspect_appealed_date', 'Accused appeal Date');
                                                    $form->text('suspect_appealed_court_name', 'Appellate court');
                                                    $form->text('suspect_appealed_court_file', 'Appeal court file number');
                                                    $form->radio('suspect_appealed_outcome', __('Appeal outcome'))
                                                        ->options([
                                                            'Upheld' => 'Upheld',
                                                            'Quashed and acquitted' => 'Quashed and acquitted',
                                                            'Quashed and retrial ordered' => 'Quashed and retrial ordered',
                                                            'On-going' => 'On-going',
                                                        ]);

                                                    $form->textarea('suspect_appeal_remarks', 'Remarks');
                                                });
                                        })
                                        ->when('in', ['Dismissed', 'Withdrawn by DPP', 'Acquittal'], function ($form) {
                                            $form->textarea('case_outcome_remarks', 'Remarks')->rules('required');
                                        })
                                        ->rules('required');
                                })
                                ->when('in', ['On-going investigation', 'On-going prosecution', 'Reinstated'], function ($form) {

                                    $form->select('suspect_court_outcome', 'Accused court case status')->options(
                                        SuspectCourtStatus::pluck('name', 'name')
                                    )
                                        ->rules('required');
                                })
                                ->rules('required');
                        })
                        ->rules('required');
                }
            })
            ->rules('required');


        if ($pendingCase->user_adding_suspect_id != Auth::user()->id) {
            $form->divider('ADD MORE SUSPECTS');
            $form->radio('add_more_suspects', __('Do you want to add more suspects to this case?'))
                ->rules('required')
                ->options([
                    'Yes' => 'Yes',
                    'No' => 'No',
                ])
                ->default('No');
        }

        $form->saved(function (Form $form) {
            if ($form->add_more_suspects == 'No') {
                return redirect(admin_url("new-exhibits-case-models/create"));
            } else {
                return redirect(admin_url("cases"));
            }
        });
        $form->saving( function ( Form $form) {
            $errors = [];
            if( $form->is_suspect_appear_in_court == 'Yes' && ($form->court_status == '' || $form->court_status == null)) {
                $errors['court_status'] = ['Court case status is required'];
            }

            if($form->is_jailed == 'No' && $form->is_fined == 'No' && $form->community_service == 'No' && $form->cautioned == 'No') {
                $errors['case_outcome'] = ['Atleast one of the following must be selected when convicted: Jailed, Fined, Community service, Cautioned'];
            }

            if(count($errors) > 0) {
                throw \Illuminate\Validation\ValidationException::withMessages($errors);
            }


        });

        return $form;
    }
}
