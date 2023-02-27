<?php

namespace App\Admin\Controllers;

use App\Models\CaseSuspect;
use App\Models\Location;
use App\Models\Offence;
use App\Models\PA;
use App\Models\Utils;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Auth;

class NewCaseSuspectController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Creating new case - suspects';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $pendingCase = Utils::hasPendingCase(Auth::user());
        if ($pendingCase != null) {
            if ($pendingCase->case_step == 1) {
                return redirect(admin_url('new-case-suspects/create'));
            } else if ($pendingCase->case_step == 2) {
                return redirect(admin_url("new-exhibits-case-models/{$pendingCase->id}/edit"));
            } else if ($pendingCase->case_step == 3) {
            } else {
            }
            return redirect(admin_url("new-confirm-case-models/{$pendingCase->id}/edit"));           //dd($pendingCase);
        }

        return redirect(admin_url("cases"));

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
        $grid->column('arrest_agency', __('Arrest agency'));
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
        $grid->column('use_same_court_information', __('Use same court information'));
        $grid->column('suspect_number', __('Suspect number'));
        $grid->column('arrest_in_pa', __('Arrest in pa'));
        $grid->column('pa_id', __('Pa id'));
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
                return redirect(admin_url('new-case-suspects/create'));
            } else if ($pendingCase->case_step == 2) {
                return redirect(admin_url("new-exhibits-case-models/{$pendingCase->id}/edit"));
            } else if ($pendingCase->case_step == 3) {
            } else {
            }
            return redirect(admin_url("new-confirm-case-models/{$pendingCase->id}/edit"));           //dd($pendingCase);
        }

        return redirect(admin_url("new-confirm-case-models/{$pendingCase->id}/edit"));           //dd($pendingCase);
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
        $show->field('arrest_latitude', __('Arrest latitude'));
        $show->field('arrest_longitude', __('Arrest longitude'));
        $show->field('arrest_first_police_station', __('Arrest first police station'));
        $show->field('arrest_current_police_station', __('Arrest current police station'));
        $show->field('arrest_agency', __('Arrest agency'));
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
        $show->field('use_same_court_information', __('Use same court information'));
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
                    return redirect(admin_url('new-case-suspects/create'));
                } else if ($pendingCase->case_step == 2) {
                    return redirect(admin_url("new-exhibits-case-models/{$pendingCase->id}/edit"));
                } else if ($pendingCase->case_step == 3) {
                } else {
                }
                return redirect(admin_url("new-confirm-case-models/{$pendingCase->id}/edit"));           //dd($pendingCase);
            }
        });

        $pendingCase = Utils::hasPendingCase(Auth::user());
        if ($pendingCase == null) {
            die("active case not found.");
        } else {
            $form->hidden('case_id', 'Suspect photo')->default($pendingCase->id)->value($pendingCase->id);
        }

        Admin::css(url('/css/new-case.css'));

        $form->disableCreatingCheck();
        $form->disableReset();
        $form->disableEditingCheck();
        $form->disableViewCheck();
        $form->html(view('steps', ['active' => 2, 'case' => $pendingCase]));




        $form->divider('Suspect Bio data');


        if (count($pendingCase->suspects) > 0) {
            $form->html('<a class="btn btn-danger" href="' . admin_url("new-exhibits-case-models/{$pendingCase->id}/edit") . '" >SKIP TO EXHIBITS</a>', 'SKIP');
        }
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
            if ($pendingCase->suspects->count() > 0) {
                $hasPendingSusps = true;
            }
        }


        if (!$hasPendingSusps) {
            $form->listbox('offences', 'Offences')->options(Offence::all()->pluck('name', 'id'))
            ->help("Select offences involded in this case")
            ->rules('required'); 
        } else {
            $form->radio('use_offence', "Do you want to apply same offence for previous suspects?")
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
                        if ($pendingCase->suspects->count() > 0) {
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
                                                $form->decimal('fined_amount', 'File amount')->help("(In UGX)");
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
                                                $form->date('suspect_appealed_date', 'Suspect appeal Date');
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











        $form->divider('ADD MORE SUSPECTS');

        $form->radio('add_more_suspects', __('Do you want to add more suspects to this case?'))
            ->rules('required')
            ->options([
                'Yes' => 'Yes',
                'No' => 'No',
            ]);

        /*      if (count($pendingCase->suspects) > 0) {
            $form->html('<a class="btn btn-danger" href="' . admin_url("new-exhibits-case-models/{$pendingCase->id}/edit") . '" >SKIP TO EXHIBITS</a>', 'SKIP');
        } */

        return $form;
    }
}
