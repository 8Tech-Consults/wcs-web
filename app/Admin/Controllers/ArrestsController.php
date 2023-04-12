<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\CaseModel\AddArrest;
use App\Admin\Actions\CaseModel\AddCourte;
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
        $statuses = [1, 2, 3];

        $pendingCase = Utils::hasPendingCase(Auth::user());
        if ($pendingCase != null) {
            Admin::script('window.location.replace("' . admin_url('new-case-suspects/create') . '");');
            return 'Loading...';
        }



        $grid = new Grid(new CaseSuspect());
        $u = Auth::user();
        if ($u->isRole('ca-agent')) {
            $grid->model()->where([
                'reported_by' => $u->id
            ]);
            $grid->disableExport();
        } else if (
            $u->isRole('ca-team') ||
            $u->isRole('ca-manager') ||
            $u->isRole('hq-team-leaders')
        ) {
            $grid->model()->where([
                'ca_id' => $u->ca_id
            ])->orWhere([
                'reported_by' => $u->id
            ]);
        } else if (!$u->isRole('admin')) {
            $grid->model()->where([
                'ca_id' => $u->ca_id
            ]);
        }

        $grid->export(function ($export) {

            $export->filename('Arrests');

            $export->except(['actions']);

            $export->column('is_jailed', function ($value, $original) {
                if (
                    $original == 1 ||
                    $original == 'Yes'
                ) {
                    return 'Yes';
                } else {
                    return 'No';
                }
            });
            $export->column('is_suspect_appear_in_court', function ($value, $original) {
                if (
                    $original == 1 ||
                    $original == 'Yes'
                ) {
                    return 'Yes';
                } else {
                    return 'No';
                }
            });

            $export->column('is_suspects_arrested', function ($value, $original) {
                if (
                    $original == 1 ||
                    $original == 'Yes'
                ) {
                    return 'Yes';
                } else {
                    return 'No';
                }
            });

            $export->column('national_id_number', function ($value, $original) {
                return  $original;
            });

            // $export->only(['column3', 'column4' ...]);

            $export->originalValue(['suspects_count', 'exhibit_count']);
            $export->column('status', function ($value, $original) {

                if ($value == 0) {
                    return 'Pending';
                } else if ($value == 1) {
                    return 'Active';
                } {
                }
                return 'Closed';
            });
        });







        $grid->disableBatchActions();
        $grid->disableCreateButton();



        $grid->model()
            ->where([
                'is_suspects_arrested' => 'Yes',
            ])
            ->where(
                'is_suspect_appear_in_court',
                '!=',
                'Yes'
            )
            ->orderBy('id', 'Desc');

        $u = Auth::user();
        if ($u->isRole('ca-agent')) {
            $grid->model()->where([
                'reported_by' => $u->id
            ]);
        }


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

        $grid->column('id', __('ID'))->sortable()->hide();
        $grid->column('created_at', __('Date'))->hide()
            ->display(function ($x) {
                return Utils::my_date_time($x);
            })
            ->sortable();




        /* ----------------------------------------------------------------- */
        /* ---------------------------START HERE----------------------------- */
        /* ----------------------------------------------------------------- */
        $grid->column('suspect_number', __('Suspect number'))
            ->sortable();
        $grid->column('first_name', __('Name'))
            ->display(function ($x) {
                return $this->first_name . " " . $this->middle_name . " " . $this->last_name;
            })
            ->sortable();
        $grid->column('is_suspects_arrested', 'At Police')
            ->dot([
                null => 'danger',
                0 => 'danger',
                'No' => 'danger',
                'Yes' => 'success',
                1 => 'success',
            ], 'danger')
            ->sortable();


        $grid->column('management_action', 'Managment action')->hide();
        $grid->column('not_arrested_remarks', 'Managment remarks')->hide();
        $grid->column('arrest_date_time', 'Arrest date')
            ->display(function ($d) {
                return Utils::my_date($d);
            });
        $grid->column('arrest_in_pa', __('Arrest in P.A'))
            ->display(function ($x) {
                if ($x == 'Yes' || $x == 1) {
                    return 'Yes';
                }
                return 'No';
            })
            ->dot([
                null => 'danger',
                0 => 'danger',
                'No' => 'danger',
                'Yes' => 'success',
                1 => 'success',
            ], 'danger')->sortable();

        $grid->column('pa_id', 'P.A of Arrest ')
            ->display(function ($x) {

                return $this->arrestPa->name;
            })
            ->sortable();
        $grid->column('ca_id', 'C.A')
            ->display(function ($x) {
                if ($this->arrestCa == null) {
                    return '-';
                }
                return $this->arrestCa->name;
            })
            ->sortable();
        $grid->column('arrest_district_id', __('District'))
            ->display(function ($x) {
                return Utils::get('App\Models\Location', $this->arrest_district_id)->name_text;
            })
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
        $grid->column('arrest_first_police_station', 'First police station')->display(function ($x) {
            if ($x == null || strlen($x) < 2) {
                return '-';
            }
            return $x;
        })->sortable();
        $grid->column('arrest_current_police_station', 'Current police station')->hide()->sortable();
        $grid->column('arrest_agency', 'Arrest agency')->display(function ($x) {
            if ($x == null || strlen($x) < 2) {
                return '-';
            }
            return $x;
        })->sortable();
        $grid->column('arrest_uwa_unit')->display(function ($x) {
            if ($x == null || strlen($x) < 2) {
                return '-';
            }
            return $x;
        })->sortable();
        $grid->column('arrest_crb_number')->hide()->sortable();
        $grid->column('police_sd_number')->sortable();
        $grid->column('is_suspect_appear_in_court', __('Appeared Court'))
            ->using([
                null => 'No',
                0 => 'No',
                'No' => 'No',
                'Yes' => 'Yes',
                1 => 'Yes',
            ], 'danger')
            ->dot([
                null => 'danger',
                0 => 'danger',
                'No' => 'danger',
                'Yes' => 'success',
                1 => 'success',
            ], 'danger')->sortable();
        $grid->column('status', 'Case status')->hide()->sortable();
        $grid->column('police_action', __('Police action'))->hide();
        /*         $grid->column('case_outcome', 'Case ouctome at Police level')->hide()->sortable(); */
        $grid->column('police_action_date', __('Police action date'))->hide();
        $grid->column('police_action_remarks', __('Police remarks'))->hide();


        $grid->actions(function ($actions) {

            if (
                Auth::user()->isRole('hq-team-leaders') ||
                Auth::user()->isRole('ca-team')
            ) {
            }
            $actions->disableDelete();

            $actions->add(new AddCourte);
        });


        return $grid;
    }


    protected function form()
    {

        $form = new Form(new CaseSuspect());

        $arr = (explode('/', $_SERVER['REQUEST_URI']));
        $pendingCase = null;
        $ex = CaseSuspect::find($arr[2]);
        if ($ex != null) {
            $pendingCase = CaseModel::find($ex->case_id);
        } else {
            die("Exhibit not found.");
        }
        if ($pendingCase == null) {
            die("Case not found.");
        }

        if ($pendingCase == null) {
            die("active case not found.");
        } else {
            $form->hidden('case_id', 'Suspect photo')->default($pendingCase->id)->value($pendingCase->id);
        }

        $form->display('SUSPECT')->default($ex->uwa_suspect_number);
        $form->divider();

        $form->disableCreatingCheck();
        $form->disableReset();
        $form->disableEditingCheck();
        $form->disableViewCheck();





        $form->radio('is_suspects_arrested', "Has suspect been handed over to police?")
            ->options([
                'Yes' => 'Yes',
                'No' => 'No',
            ])
            ->rules('required')
            ->when('No', function ($form) {
                $form->select('management_action', 'Action taken by management')->options([
                    'Fined' => 'Fined',
                    'Cautioned and Released' => 'Cautioned and Released',
                    'At Large' => 'At Large',
                ]);

                $form->textarea('not_arrested_remarks', 'Remarks');
            })
            ->when('Yes', function ($form) {


                $form->divider('Arrest information');

                $hasPendingSusps = null;
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

                            $form->date('arrest_date_time', 'Arrest date and time');

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
                                })
                                ->when('No', function ($form) {
                                    $form->select('arrest_sub_county_id', __('Sub county of Arrest'))
                                        ->rules('required')
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
                            ])
                                ->when('UWA', function ($form) {
                                    $form->select('arrest_uwa_unit', 'UWA Unit')->options([
                                        'Canine Unit' => 'The Canine Unit',
                                        'WCU' => 'WCU',
                                        'NRCN' => 'NRCN',
                                        'LEU' => 'LEU',
                                    ]);
                                });

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
                        ->rules('required')
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
                        ->rules('required');
                } else {


                    $form->date('arrest_date_time', 'Arrest date and time');

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
                    ])
                        ->when('UWA', function ($form) {
                            $form->select('arrest_uwa_unit', 'UWA Unit')->options([
                                'Canine Unit' => 'The Canine Unit',
                                'WCU' => 'WCU',
                                'NRCN' => 'NRCN',
                                'LEU' => 'LEU',
                            ]);
                        });


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
                                });
                        })->when('Yes', function ($form) {
                            $supects = [];
                            $pendingCase = Utils::hasPendingCase(Auth::user());
                            if ($pendingCase != null) {
                                if ($pendingCase->suspects->count() > 0) {
                                    foreach ($pendingCase->suspects as $sus) {
                                        $supects[$sus->id] = $sus->uwa_suspect_number . " - " . $sus->name;
                                    }
                                }
                            }
                            $form->select('use_same_court_information_id', 'Select suspect')
                                ->options($supects)
                                ->rules('required');
                        });
                }
            });


        return $form;
    }
}
