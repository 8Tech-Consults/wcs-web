<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\CaseModel\AddArrest;
use App\Admin\Actions\CaseModel\AddCourte;
use App\Admin\Actions\CaseModel\EditArrest;
use App\Admin\Actions\CaseModel\ViewSuspect;
use App\Models\ArrestingAgency;
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
use Illuminate\Support\Facades\DB;
use App\Rules\AfterDateInDatabase;

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

        $grid->export(function ($export) {

            $export->filename('Arrests');

            $export->except(['actions']);

            $export->column('arrest_in_pa', function ($value, $original) {
                if (
                    $original == 1 ||
                    $original == 'Yes'
                ) {
                    return 'Yes';
                } else {
                    return 'No';
                }
            });

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
            // $export->column('status', function ($value, $original) {

            //     if ($value == 0) {
            //         return 'Pending';
            //     } else if ($value == 1) {
            //         return 'Active';
            //     } {
            //     }
            //     return 'Closed';
            // });
        });







        $grid->disableBatchActions();
        $grid->disableCreateButton();



        $grid->model()
            ->where([
                'is_suspects_arrested' => 'Yes',
            ])
            /*  ->where(
                'is_suspect_appear_in_court',
                '!=',
                'Yes'
            ) */
            ->orderBy('updated_at', 'Desc');


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
        $grid->quickSearch(function ($model, $query) {
            $model->where(DB::raw("CONCAT(first_name, ' ',middle_name,' ', last_name)"), 'like', "%{$query}%")
                ->orWhere(DB::raw("CONCAT(middle_name,' ',first_name,' ', last_name)"), 'like', "%{$query}%")
                ->orWhere(DB::raw("CONCAT(first_name,' ', last_name, ' ', middle_name)"), 'like', "%{$query}%")
                ->orWhere(DB::raw("CONCAT(last_name,' ', first_name, ' ', middle_name)"), 'like', "%{$query}%")
                ->orWhere(DB::raw("CONCAT(last_name,' ', middle_name, ' ', first_name)"), 'like', "%{$query}%")
                ->orWhere(DB::raw("CONCAT(middle_name,' ', last_name, ' ', first_name)"), 'like', "%{$query}%")
                ->orWhere(DB::raw("CONCAT(first_name,' ', middle_name)"), 'like', "%{$query}%")
                ->orWhere(DB::raw("CONCAT(first_name,' ', last_name)"), 'like', "%{$query}%")
                ->orWhere(DB::raw("CONCAT(last_name,' ', first_name)"), 'like', "%{$query}%")
                ->orWhere(DB::raw("CONCAT(last_name,' ', middle_name)"), 'like', "%{$query}%")
                ->orWhere(DB::raw("CONCAT(middle_name,' ', first_name)"), 'like', "%{$query}%")
                ->orWhere(DB::raw("CONCAT(middle_name,' ', last_name)"), 'like', "%{$query}%")
                ->orWhere('middle_name', 'like', "%{$query}%")
                ->orWhere('first_name', 'like', "%{$query}%")
                ->orWhere('last_name', 'like', "%{$query}%");
        })->placeholder('Search by suspect\'s names');
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
        $grid->column('arrest_location', 'Arrest Location')->display(function () {
            if ($this->arrest_in_pa == 'Yes') {
                return $this->arrest_village;
            }
            return '-';
        })->hide()->sortable();

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
        $grid->column('arrest_village')->display(function () {
            if ($this->arrest_in_pa == 'Yes') {
                return '-';
            }
            return $this->arrest_village;
        })->hide()->sortable();
        $grid->column('arrest_latitude', 'Arrest GPS latitude')->hide()->sortable();
        $grid->column('arrest_longitude', 'Arrest GPS longitude')->hide()->sortable();
        $grid->column('arrest_first_police_station', 'First police station')->display(function ($x) {
            if ($x == null || strlen($x) < 2) {
                return '-';
            }
            return $x;
        })->sortable();
        $grid->column('arrest_current_police_station', 'Current police station')->hide()->sortable();
        $grid->column('arrest_agency', 'Lead Arrest agency')->display(function ($x) {
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
        $grid->column('other_arrest_agencies', 'Other Arrest Agencies')->display(function ($array) {
            if (!is_array($array)) {
                return '-';
            }
            if (count($array) < 1) {
                return '-';
            }
            $str = '';

            foreach ($array as $key => $value) {
                if ($key == count($array) - 1) {
                    $str .= $value;
                } else {
                    $str .= $value . ', ';
                }
            }
            return $str;
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
        $grid->column('status', 'Case status')->display(function ($form) {
            if ($this->status == null) {
                return '-';
            }
            return $this->status;
        })->hide()->sortable();
        $grid->column('police_action', __('Police action'))->hide();
        /*         $grid->column('case_outcome', 'Case ouctome at Police level')->hide()->sortable(); */
        $grid->column('police_action_date', __('Police action date'))->hide();
        $grid->column('police_action_remarks', __('Police remarks'))->hide();


        $grid->actions(function ($actions) {
            $user = Admin::user();
            $row = $actions->row;

            $actions->disableView();
            $actions->disableDelete();
            $actions->disableedit();
            $actions->add(new ViewSuspect);


            $can_add_court = false;
            $can_edit = false;

            if ($user->isRole('ca-agent')) {
                if (
                    $row->reported_by == $user->id
                ) {
                    $can_add_suspect = true;
                    $can_add_exhibit = true;
                    $can_add_comment = true;
                    $can_add_arrest = true;
                    $can_add_court = true;
                    $can_edit = false;
                }
            } elseif ($user->isRole('ca-team')) {
                if (
                    $row->reported_by == $user->id ||
                    $row->created_by_ca_id == $user->ca_id ||
                    $row->case->ca_id == $user->ca_id
                ) {
                    $can_add_suspect = true;
                    $can_add_exhibit = true;
                    $can_add_comment = true;
                    $can_add_arrest = true;
                    $can_add_court = true;
                    $can_edit = false;
                }
            } elseif ($user->isRole('ca-manager')) {
                if (
                    $row->reported_by == $user->id ||
                    $row->created_by_ca_id == $user->ca_id ||
                    $row->case->ca_id == $user->ca_id
                ) {
                    $can_add_suspect = true;
                    $can_add_exhibit = true;
                    $can_add_comment = true;
                    $can_add_court_info = true;
                    $can_add_arrest = true;
                    $can_edit = true;
                    $can_add_court = true;
                }
            } elseif ($user->isRole('hq-team-leaders')) {
                if (
                    $row->reported_by == $user->id
                ) {
                    $can_add_suspect = true;
                    $can_add_exhibit = true;
                    $can_add_comment = true;
                    $can_add_court = true;
                    
                }
            } elseif ($user->isRole('hq-manager')) {
                $can_add_comment = true;
                $can_add_court_info = true;
                $can_edit = true;
            } elseif ($user->isRole('director')) {
            } elseif ($user->isRole('secretary')) {
            } elseif (
                $user->isRole('hq-prosecutor')
            ) {
                $can_add_comment = true;
                $can_add_court = true;
                $can_edit = false;
            } elseif ($user->isRole('prosecutor')) {
                if (
                    $row->case->created_by_ca_id == $user->ca_id ||
                    $row->case->ca_id == $user->ca_id
                ) {
                    $can_add_comment = true;
                    $can_add_court = true;
                    $can_edit = false;
                }
            } else if (
                $user->isRole('admin') ||
                $user->isRole('administrator')
            ) {
                $can_add_suspect = true;
                $can_add_exhibit = true;
                $can_add_comment = true;
                $can_add_court_info = true;
                $can_edit = true;
                $can_add_arrest = true;
                $can_add_court = true;
                $can_edit = true;
            }

            $is_active  = true;
            $case = $row->case;
            if (
                !$user->isRole('admin')
            ) {
                if (strtolower($case->court_status) == 'concluded') {
                    $is_active = false;
                }
            }
            if (!$is_active) {
                return;
            }

            if (!$user->isRole('admin')) {
                if ($row->is_suspect_appear_in_court == 'Yes') {
                    $can_add_court = false;
                }
            }



            if ($can_add_court) {
                $actions->add(new AddCourte);
            }

            if ($can_edit) {
                $actions->add(new EditArrest);
            }

            return $actions;



            if ($user->isRole('admin') || $user->isRole('hq-team-leaders') || $user->isRole('hq-manager') || $user->isRole('ca-team') || $user->isRole('ca-agent') || $user->isRole('director') || $user->isRole('ca-manager')) {

                if ($actions->row->is_suspect_appear_in_court != 'Yes') {
                    if ($user->isRole('admin') || $user->isRole('hq-team-leaders') || $user->isRole('hq-manager') || $user->isRole('director')) {
                        $actions->add(new AddCourte);
                    } else {
                        if ($actions->row->reported_by == $user->id) {
                            $actions->add(new AddCourte);
                        }
                    }
                }

                //Give dit rights to only admin and ca-manager of that ca
                if ($user->isRole('admin') || $user->isRole('ca-manager')) {
                    if ($user->isRole('ca-manager')) {
                        if ($user->ca_id == $actions->row->ca_id) {
                            $actions->add(new EditArrest);
                        }
                    } else {
                        $actions->add(new EditArrest);
                    }
                }
            }
        });


        return $grid;
    }


    protected function form()
    {

        $form = new Form(new CaseSuspect());

        $arr = (explode('/', $_SERVER['REQUEST_URI']));


        $ex = CaseSuspect::find($arr[2]);
        if ($ex == null) {
            foreach ($arr as $key => $val) {
                $ex = CaseSuspect::find($val);
                if ($ex != null) {
                    break;
                }
            }
        }

        $pendingCase = Utils::get_edit_case();
        if ($ex != null) {
            $pendingCase = CaseModel::find($ex->case_id);
        } else {
            die("Case not found.");
        }
        if ($pendingCase == null) {
            die("Case not found.");
        }

        if ($pendingCase == null) {
            Admin::script('window.location.replace("' . admin_url("cases") . '");');
            return 'Loading...';
        } else {
            $form->hidden('case_id', 'Suspect photo')->default($pendingCase->id)->value($pendingCase->id);
        }

        $form->display('SUSPECT')->default($ex->uwa_suspect_number);
        $form->divider();

        /*  $crb_no = null;
        foreach ($pendingCase->suspects as $key => $val) {
            dd($val);
        } */

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
                ])->rules('required');

                $form->textarea('not_arrested_remarks', 'Remarks')->rules('required');
            })
            ->when('Yes', function ($form) {


                $form->divider('Arrest information');

                $hasPendingSusps = null;
                $csb = null;
                $pendingCase = Utils::get_edit_case();
                $pendingCase = Utils::get_edit_case();
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
                            $pendingCase = Utils::get_edit_case();
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
                            $form->select('arrest_agency', 'Lead Arresting agency')->options(
                                ArrestingAgency::orderBy('name', 'Desc')->pluck('name', 'name')
                            )
                                ->when('UWA', function ($form) {
                                    $form->select('arrest_uwa_unit', 'UWA Unit')->options([
                                        'Canine Unit' => 'The Canine Unit',
                                        'WCU' => 'WCU',
                                        'LEU' => 'LEU',
                                    ]);
                                });
                            $form->multipleSelect('other_arrest_agencies', 'Other arresting agencies')->options(
                                ArrestingAgency::orderBy('name', 'Desc')->pluck('name', 'name')
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
                            $pendingCase = Utils::get_edit_case();
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
                        ArrestingAgency::orderBy('name', 'Desc')->pluck('name', 'name')
                    )
                        ->when('UWA', function ($form) {
                            $form->select('arrest_uwa_unit', 'UWA Unit')->options([
                                'Canine Unit' => 'The Canine Unit',
                                'WCU' => 'WCU',
                                'LEU' => 'LEU',
                            ]);
                        });
                    $form->multipleSelect('other_arrest_agencies', 'Other arresting agencies')->options(
                        ArrestingAgency::orderBy('name', 'Desc')->pluck('name', 'name')
                    );


                    $hasPendingSusps = false;
                    $csb = null;
                    $sd = null;
                    $pendingCase = Utils::get_edit_case();
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
                /* 
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
                            $pendingCase = Utils::get_edit_case();
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
                } */
            });


        return $form;
    }
}
