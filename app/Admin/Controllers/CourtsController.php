<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\CaseModel\EditCourtCase;
use App\Admin\Actions\CaseModel\ViewSuspect;
use App\Admin\Actions\CaseModel\CourtCaseUpdate;
use App\Models\CaseModel;
use App\Models\CaseSuspect;
use App\Models\Court;
use App\Models\Offence;
use App\Models\SuspectCourtStatus;
use App\Models\Utils;
use App\Rules\AfterDateInDatabase;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CourtsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Court Information';

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


        $grid->export(function ($export) {

            $export->except(['actions']);

            // $export->only(['column3', 'column4' ...]);


            $export->filename('Court Cases');

            $export->except(['photo', 'action']);
            // $export->originalValue(['is_jailed']);

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
            $export->column('is_fined', function ($value, $original) {
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
            $export->column('suspect_appealed', function ($value, $original) {
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
        });



        $grid->disableBatchActions();
        $grid->disableCreateButton();



        $grid->model()
            ->where([
                'is_suspect_appear_in_court' => 1
            ])->orwhere([
                'is_suspect_appear_in_court' => 'Yes'
            ])->orderBy('updated_at', 'Desc');

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
            $f->where(function ($query) {
                $query->whereHas('offences', function ($query) {
                    $query->where('name', 'like', "%{$this->input}%");
                });
            }, 'Filter by Offence')->select(
                Offence::pluck('name', 'name')
            );

            $f->between('case.case_date', 'Filter by case date')->date();
            $f->between('court_date', 'Filter by court date')->date();
            $f->like('court_name', 'Filter by court name');
            $f->like('court_file_number', 'Filter by court file number');
            $f->like('prosecutor', 'Filter by prosecutor');
            $f->like('magistrate_name', 'Filter by magistrate');


            $f->equal('case_outcome', 'Filter by Specific Court Case Status')->select([
                'Dismissed' => 'Dismissed',
                'Withdrawn by DPP' => 'Withdrawn by DPP',
                'Acquittal' => 'Acquittal',
                'Convicted' => 'Convicted',
            ]);

            $f->equal('suspect_court_outcome', 'Filter by Court case status')->select(
                SuspectCourtStatus::pluck('name', 'name')
            );

            $f->equal('suspect_appealed', 'Filter Accused by Appeal')->select([
                'Yes' => 'Appealed',
                'No' => 'Not Appealed'
            ]);
            $f->like('prosecutor', 'Filter prosecutor');

            //court_status
            $f->equal('court_status', 'Filter by Court case status')->select([
                'On-going prosecution' => 'On-going prosecution',
                'Reinstated' => 'Reinstated',
                'Concluded' => 'Concluded',
            ]);
            //case_outcome
            $f->equal('case_outcome', 'Filter by Specific Court Case Status')->select([
                'Dismissed' => 'Dismissed',
                'Withdrawn by DPP' => 'Withdrawn by DPP',
                'Acquittal' => 'Acquittal',
                'Convicted' => 'Convicted',
            ]);
        });

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
        })->placeholder('Search by accused names');




        $grid->column('id', __('ID'))->sortable()->hide();
        $grid->column('suspect_number', __('Accused number'))
            ->sortable();

        $grid->column('first_name', __('Accused\'s Name'))
            ->display(function ($x) {
                return $this->first_name . " " . $this->middle_name . " " . $this->last_name;
            })
            ->sortable();

        $grid->column('court_file_number')->sortable();
        $grid->column('court_date', 'Court date')
            ->display(function ($d) {
                if ($d == null) {
                    return '-';
                }
                return Utils::my_date($d);
            })->sortable();
        $grid->column('court_name')->display(function ($d) {
            if ($this->court == null) {
                return '-';
            }
            return $this->court->name;
        })->sortable();
        $grid->column('prosecutor', 'Lead prosecutor')->sortable();
        $grid->column('magistrate_name')->sortable();
        $grid->column('court_status', 'Court case status')->sortable();
        $grid->column('suspect_court_outcome', 'Accused court status')->hide()->sortable();
        $grid->column('case_outcome', 'Specific court case status')->hide()->sortable();



        $grid->column('is_jailed', __('Jailed'))
            ->display(function ($is_jailed) {
                if ($is_jailed == 1 || $is_jailed == 'Yes') {
                    return 'Yes';
                } else {
                    return 'No';
                }
            })
            ->dot([
                null => 'danger',
                0 => 'danger',
                'No' => 'danger',
                'Yes' => 'success',
                1 => 'success',
            ], 'danger')
            ->filter([
                'Yes' => 'Jailed',
                'No' => 'Not Jailed',
            ]);

        $grid->column('jail_date', 'Sentence date')
            ->hide()
            ->display(function ($d) {
                return Utils::my_date($d);
            });

        $grid->column('jail_period')->display(function ($x) {
            if ($x == null || strlen($x) < 1) {
                return "-";
            }
            return $x . " Months";
        })->sortable();
        $grid->column('prison', 'Prison')->hide()->sortable();
        $grid->column('jail_release_date', 'Date release')
            ->hide()
            ->display(function ($d) {
                return Utils::my_date($d);
            });

        $grid->column('is_fined', 'Is Fined')
            ->using([
                '1' => 'Yes',
                'Yes' => 'Yes',
                '0' => 'No',
                'No' => 'No',
            ], 'No')->label([
                1 => 'success',
                'Yes' => 'success',
                0 => 'danger',
                'No' => 'danger',
            ], 'danger')
            ->sortable();
        $grid->column('fined_amount')->display(function ($x) {
            $x = (int)($x);
            if ($x == null || strlen($x) < 1) {
                return "-";
            }
            return 'UGX ' . number_format($x);
        })->sortable();
        $grid->column('community_service')->hide()->sortable();
        $grid->column('community_service_duration', 'Duration (in hours)')->hide()->sortable();
        $grid->column('cautioned')->display(function ($x) {
            if ($x == null || strlen($x) < 1) {
                return "-";
            }
            return $x;
        })->hide()->sortable();

        $grid->column('cautioned_remarks', 'Caution remarks')->hide()->sortable();

        $grid->column('suspect_appealed', 'Accused appealed')
            ->using([
                '1' => 'Yes',
                'Yes' => 'Yes',
                '0' => 'No',
                'No' => 'No',
            ], 'No')->dot([
                1 => 'success',
                'Yes' => 'success',
                0 => 'danger',
                'No' => 'danger',
            ], 'danger')
            ->sortable();

        $grid->column('suspect_appealed_date', 'Appeal date')
            ->display(function ($d) {
                return Utils::my_date($d);
            })->hide();

        $grid->column('suspect_appealed_court_name', 'Appellate court name')
            ->hide()
            ->sortable();

        $grid->column('suspect_appealed_court_file', 'Appeal court file number')
            ->hide()
            ->sortable();

        $grid->column('suspect_appealed_outcome', 'Appeal outcome')
            ->hide()
            ->sortable();
        $grid->column('suspect_appeal_remarks', 'Appeal remarks')
            ->hide()
            ->sortable();







        $grid->column('reported_by', __('Reported by'))
            ->display(function () {

                return $this->case->reportor->name;
            })->hide()
            ->sortable();

        $grid->actions(function ($actions) {
            $user = Auth::user();
            $actions->disableView();
            $actions->disableEdit();
            if (!$user->isRole('admin')) {
                $actions->disableDelete();
            }


            $row = $actions->row;

            $can_add_suspect = false;
            $can_add_exhibit = false;
            $can_add_comment = false;
            $can_update_court_info = false;
            $can_edit = false;
            $can_modify = false;
            if ($user->isRole('ca-agent')) {
                if (
                    $row->reported_by == $user->id
                ) {
                    $can_add_suspect = true;
                    $can_add_exhibit = true;
                    $can_add_comment = true;
                    $can_add_court_info = true;
                    $can_edit = true;
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
                    $can_add_court_info = true;
                    $can_edit = true;
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
                    $can_edit = true;
                    $can_modify = true;
                }
            } elseif ($user->isRole('hq-team-leaders')) {
                if (
                    $row->reported_by == $user->id ||
                    $row->created_by_ca_id == 1
                ) {
                    $can_add_suspect = true;
                    $can_add_exhibit = true;
                    $can_add_comment = true;
                    $can_edit = true;
                }
            } elseif ($user->isRole('hq-manager')) {
                $can_add_comment = true;
                $can_edit = true;
            } elseif ($user->isRole('director')) {
            } elseif ($user->isRole('secretary')) {
            } elseif (
                $user->isRole('hq-prosecutor')  ||
                $row->created_by_ca_id == $user->ca_id ||
                $row->case->ca_id == $user->ca_id
            ) {
                $can_add_comment = true;
                $can_edit = true;
            } elseif ($user->isRole('prosecutor')) {
                if (
                    $row->created_by_ca_id == $user->ca_id ||
                    $row->ca_id == $user->ca_id
                ) {
                    $can_add_comment = true;
                    $can_edit = true;
                }
            } else if (
                $user->isRole('admin') ||
                $user->isRole('administrator')
            ) {
                $can_add_suspect = true;
                $can_add_exhibit = true;
                $can_add_comment = true;
                $can_add_court_info = true;
                $can_add_edit = true;
                $can_edit = true;
                $can_modify = true;
            }

            if (
                true
            ) {
                if (strtolower($row->court_status) == 'concluded') {
                    $can_edit = false;
                    $can_modify = false;
                }
            } else {
                $can_edit = true;
                $can_modify = true;
            }

            if ($user->isRole('admin')) {
                $can_modify = true;
            }

            $actions->add(new ViewSuspect);


            if ($can_edit) {
                $actions->add(new CourtCaseUpdate);
            }

            if ($can_modify) {
                $actions->add(new EditCourtCase);
            }

            return $actions;
            // $actions->add(new CourtCaseUpdate);

            /*  if ($user->isRole('admin') || $user->isRole('hq-team-leaders') || $user->isRole('hq-manager') || $user->isRole('ca-team') || $user->isRole('ca-agent') || $user->isRole('director') || $user->isRole('ca-manager')) {

                if ($actions->row->court_status == 'On-going prosecution' || $actions->row->court_status == 'Reinstated') {
                    if ($user->isRole('admin') || $user->isRole('hq-team-leaders') || $user->isRole('hq-manager') || $user->isRole('director')) {
                        $actions->add(new CourtCaseUpdate);
                    } else {
                        if ($actions->row->reported_by == $user->id) {
                            $actions->add(new CourtCaseUpdate);
                        }
                    }
                }
                //Give dit rights to only admin and ca-manager of that ca
                if ($user->isRole('admin') || $user->isRole('ca-manager')) {
                    if ($user->isRole('ca-manager')) {
                        if ($user->ca_id == $actions->row->ca_id) {
                            $actions->add(new EditCourtCase);
                        }
                    } else {
                        $actions->add(new EditCourtCase);
                    }
                }
            } */
        });




        return $grid;
    }





    protected function form()
    {

        $form = new Form(new CaseSuspect());

        $arr = (explode('/', $_SERVER['REQUEST_URI']));
        $pendingCase = null;

        $pendingCase = Utils::get_edit_case();
        $ex = Utils::get_edit_suspect();

        if ($ex == null || $pendingCase == null) {
            die("Suspect or case not found.");
        }
        if ($pendingCase == null) {
            Admin::script('window.location.replace("' . admin_url("cases") . '");');
            return 'Loading...';
        } else {
            $form->hidden('case_id', 'Suspect photo')->default($pendingCase->id)->value($pendingCase->id);
        }

        $form->display('ACCUSED')->default($ex->uwa_suspect_number);
        $form->divider();

        $form->disableCreatingCheck();
        $form->disableReset();
        $form->disableEditingCheck();
        $form->disableViewCheck();


        $pendingCase = Utils::get_edit_case();
        $ex = Utils::get_edit_suspect();
        if ($ex != null) {
            $pendingCase = CaseModel::find($ex->case_id);
        } else {
            die("Accused not found.");
        }
        if ($pendingCase == null) {
            die("Case not found.");
        }

        if ($pendingCase == null) {
            Admin::script('window.location.replace("' . admin_url("cases") . '");');
            return 'Loading...';
        } else {
            $form->hidden('case_id', 'Accused photo')->default($pendingCase->id)->value($pendingCase->id);
        }
        $csb = null;
        $pendingCase = Utils::get_edit_case();
        $ex = Utils::get_edit_suspect();
        if ($pendingCase != null) {
            if ($pendingCase->suspects->count() > 0) {
                $hasPendingSusps = true;
                $csb = $pendingCase->getCrbNumber();
            }
        }

        //  check if editing or updating
        if (session('court_case_action') == 'update') {
            $form->hidden('is_suspect_appear_in_court')->default('Yes');
            $form->select('court_status', __('Court case status'))
                ->options([
                    'On-going prosecution' => 'On-going prosecution',
                    'Reinstated' => 'Reinstated',
                    'Concluded' => 'Concluded',
                ])->when('Concluded', function ($form) {

                    $form->select('case_outcome', 'Specific court case status')->options([
                        'Dismissed' => 'Dismissed',
                        'Withdrawn by DPP' => 'Withdrawn by DPP',
                        'Acquittal' => 'Acquittal',
                        'Convicted' => 'Convicted',
                    ])
                        ->rules('required')
                        ->when('Convicted', function ($form) {
                            $form->radio('is_jailed', __('Was Accused jailed?'))
                                ->options([
                                    "Yes" => 'Yes',
                                    "No" => 'No',
                                ])
                                ->when('Yes', function ($form) {
                                    $form->date('jail_date', 'Sentence date')->rules(
                                        function (Form $form) {
                                            return [new AfterDateInDatabase('case_suspects', $form->model()->id, 'court_date')];
                                        }
                                    );
                                    $form->decimal('jail_period', 'Jail period')->help("(In months)")
                                        ->rules('required');
                                    $form->text('prison', 'Prison name');
                                    $form->date('jail_release_date', 'Date released');
                                })
                                ->default('No');
                            $form->radio('is_fined', __('Was Accused fined?'))
                                ->options([
                                    'Yes' => 'Yes',
                                    'No' => 'No',
                                ])
                                ->when('Yes', function ($form) {
                                    $form->decimal('fined_amount', 'Fine amount')->help("(In UGX)");
                                })
                                ->default('No');

                            $form->radio('community_service', __('Was the Accused offered community service?'))
                                ->options([
                                    'Yes' => 'Yes',
                                    'No' => 'No',
                                ])
                                ->when('Yes', function ($form) {
                                    $form->decimal(
                                        'community_service_duration',
                                        'Community service duration (in Hours)'
                                    )
                                        ->rules('required');
                                })
                                ->default('No');


                            $form->radio('cautioned', __('Was Accused cautioned?'))
                                ->options([
                                    'Yes' => 'Yes',
                                    'No' => 'No',
                                ])
                                ->when('Yes', function ($form) {
                                    $form->text('cautioned_remarks', 'Enter caution remarks')
                                        ->rules('required');
                                })
                                ->default('No');

                            $form->radio('suspect_appealed', __('Did the Accused appeal?'))
                                ->options([
                                    'Yes' => 'Yes',
                                    'No' => 'No',
                                ])
                                ->default('No')
                                ->when('Yes', function ($form) {
                                    $form->date('suspect_appealed_date', 'Accused appeal Date')
                                        ->rules('required');
                                    $form->text('suspect_appealed_court_name', 'Appellate court')->rules('required');
                                    $form->text('suspect_appealed_court_file', 'Appeal court file number')->rules('required');
                                    $form->select('suspect_appealed_outcome', __('Appeal outcome'))
                                        ->options([
                                            'Upheld' => 'Upheld',
                                            'Quashed and acquitted' => 'Quashed and acquitted',
                                            'Quashed and retrial ordered' => 'Quashed and retrial ordered',
                                            'On-going' => 'On-going',
                                        ])->rules('required');

                                    $form->textarea('suspect_appeal_remarks', 'Remarks');
                                });
                        })
                        ->when('in', ['Dismissed', 'Withdrawn by DPP', 'Acquittal'], function ($form) {
                            $form->textarea('case_outcome_remarks', 'Remarks')->rules('required');
                        });
                })
                ->when('in', ['On-going investigation', 'On-going prosecution', 'Reinstated'], function ($form) {


                    $form->select('suspect_court_outcome', 'Accused court case status')->options(
                        SuspectCourtStatus::pluck('name', 'name')
                    )->rules('required');
                })
                ->rules('required');
        } else {
            $form->select('is_suspect_appear_in_court', __('Has this Accused appeared in court?'))
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
                            $form->select('police_action', 'Case status at police level')->options([
                                'Police bond' => 'Police bond',
                                'Skipped bond' => 'Skipped bond',
                                'Under police custody' => 'Under police custody',
                                'Escaped from colice custody' => 'Escaped from police custody',
                            ])
                                ->rules('required');
                        })
                        ->when('Closed', function ($form) {
                            $form->select('police_action', 'Case status at police level')->options([
                                'Dismissed by state' => 'Dismissed by state',
                                'Withdrawn by complainant' => 'Withdrawn by complainant',
                            ]);
                            $form->date('police_action_date', 'Date');
                            $form->textarea('police_action_remarks', 'Remarks');
                        })->when('Re-opened', function ($form) {
                            $form->select('police_action', 'Case status at police level')->options([
                                'Police bond' => 'Police bond',
                                'Skipped bond' => 'Skipped bond',
                                'Under police custody' => 'Under police custody',
                                'Escaped from colice custody' => 'Escaped from police custody',
                            ]);
                            $form->date('police_action_date', 'Date');
                            $form->textarea('police_action_remarks', 'Remarks');
                        });
                })
                ->when('Yes', function ($form) {

                    $form->divider('Court information');
                    $courtFileNumber = null;
                    $pendingCase = Utils::get_edit_case();
                    $ex = Utils::get_edit_suspect();
                    if ($pendingCase != null) {
                        $courtFileNumber = $pendingCase->getCourtFileNumber();
                    }

                    if ($courtFileNumber == null) {
                        $form->text('court_file_number', 'Court file number')->rules('required');
                    } else {

                        $user = Admin::user();
                        if ($user->isRole('admin')) {
                            $form->text('court_file_number', 'Court file number')
                                ->default($courtFileNumber)
                                ->value($courtFileNumber)
                                ->rules('required');
                        } else {
                            $form->text('court_file_number', 'Court file number')
                                ->default($courtFileNumber)
                                ->value($courtFileNumber)
                                ->readonly()
                                ->rules('required');
                        }
                    }
                    // dd($form->model()->getKey());
                    $form->date('court_date', 'Court Date of first appearance')->rules(
                        function (Form $form) {
                            return ['required', new AfterDateInDatabase('case_suspects', $form->model()->id, 'arrest_date_time')];
                        }
                    );

                    $courts =  Court::where([])->orderBy('id', 'desc')->get()->pluck('name', 'id');
                    $form->select('court_name', 'Select Court')->options($courts)
                        ->when(1, function ($form) {
                            $form->text('other_court_name', 'Specify other court name')
                                ->rules('required');
                        })
                        ->rules('required');

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
                    ))->rules('required'); */
                    $form->text('prosecutor', 'Lead prosecutor');
                    $form->text('magistrate_name', 'Magistrate Name');


                    $form->select('court_status', __('Court case status'))
                        ->options([
                            'On-going prosecution' => 'On-going prosecution',
                            'Reinstated' => 'Reinstated',
                            'Concluded' => 'Concluded',
                        ])->when('Concluded', function ($form) {

                            $form->select('case_outcome', 'Specific court case status')->options([
                                'Dismissed' => 'Dismissed',
                                'Withdrawn by DPP' => 'Withdrawn by DPP',
                                'Acquittal' => 'Acquittal',
                                'Convicted' => 'Convicted',
                            ])
                                ->when('Convicted', function ($form) {
                                    $form->radio('is_jailed', __('Was Accused jailed?'))
                                        ->options([
                                            "Yes" => 'Yes',
                                            "No" => 'No',
                                        ])
                                        ->when('Yes', function ($form) {
                                            $form->date('jail_date', 'Sentence date')->rules('after_or_equal:court_date|required');
                                            $form->decimal('jail_period', 'Jail period')->help("(In months)")->rules('required');
                                            $form->text('prison', 'Prison name');
                                            $form->date('jail_release_date', 'Date released');
                                        })
                                        ->default('No');
                                    $form->radio('is_fined', __('Was Accused fined?'))
                                        ->options([
                                            'Yes' => 'Yes',
                                            'No' => 'No',
                                        ])
                                        ->when('Yes', function ($form) {
                                            $form->decimal('fined_amount', 'Fine amount')->help("(In UGX)")
                                                ->rules('required');
                                        })
                                        ->default('No');

                                    $form->radio('community_service', __('Was the Accused offered community service?'))
                                        ->options([
                                            'Yes' => 'Yes',
                                            'No' => 'No',
                                        ])
                                        ->when('Yes', function ($form) {
                                            $form->decimal(
                                                'community_service_duration',
                                                'Community service duration (in Hours)'
                                            )
                                                ->rules('required');
                                        })
                                        ->default('No');

                                    $form->radio('cautioned', __('Was Accused cautioned?'))
                                        ->options([
                                            'Yes' => 'Yes',
                                            'No' => 'No',
                                        ])
                                        ->when('Yes', function ($form) {
                                            $form->text('cautioned_remarks', 'Enter caution remarks')
                                                ->rules('required');
                                        })
                                        ->default('No');

                                    $form->radio('suspect_appealed', __('Did the Accused appeal?'))
                                        ->options([
                                            'Yes' => 'Yes',
                                            'No' => 'No',
                                        ])
                                        ->when('Yes', function ($form) {
                                            $form->date('suspect_appealed_date', 'Accused appeal Date')
                                                ->rules('required');
                                            $form->text('suspect_appealed_court_name', 'Appellate court')
                                                ->rules('required');
                                            $form->text('suspect_appealed_court_file', 'Appeal court file number')
                                                ->rules('required');
                                            $form->select('suspect_appealed_outcome', __('Appeal outcome'))
                                                ->options([
                                                    'Upheld' => 'Upheld',
                                                    'Quashed and acquitted' => 'Quashed and acquitted',
                                                    'Quashed and retrial ordered' => 'Quashed and retrial ordered',
                                                    'On-going' => 'On-going',
                                                ])->rules('required');

                                            $form->textarea('suspect_appeal_remarks', 'Remarks')->rules('required');
                                        });
                                })
                                ->when('in', ['Dismissed', 'Withdrawn by DPP', 'Acquittal'], function ($form) {
                                    $form->textarea('case_outcome_remarks', 'Remarks')->rules('required');
                                });
                        })
                        ->when('in', ['On-going investigation', 'On-going prosecution', 'Reinstated'], function ($form) {


                            $form->select('suspect_court_outcome', 'Accused court case status')->options(
                                SuspectCourtStatus::pluck('name', 'name')
                            )->rules('required');
                        })
                        ->rules('required');
                })
                ->rules('required');
        }

        $form->submitted(function (Form $form) {
        });

        $form->saved(function (Form $form) {
            session()->forget('court_case_action');
        });
        $form->saving(function (Form $form) {
            // if(session('court_case_action') == 'update'){
            if ($form->case_outcome == 'Convicted' && $form->is_jailed == 'No' && $form->is_fined == 'No' && $form->community_service == 'No' && $form->cautioned == 'No') {
                throw \Illuminate\Validation\ValidationException::withMessages(['case_outcome' => ['Atleast one of the following must be selected when convicted: Jailed, Fined, Community service, Cautioned']]);
            }
            // }

        });


        return $form;
    }
}
