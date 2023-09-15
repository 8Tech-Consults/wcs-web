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

        $u = Auth::user();
        if ($u->isRole('ca-agent')) {
            $grid->model()->where([
                'reported_by' => $u->id
            ]);
        } else if (
            $u->isRole('ca-team')
        ) {
            $grid->model()->where([
                'ca_id' => $u->ca_id
            ])->orWhere([
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
            $f->where(function ($query) {
                $query->whereHas('offences', function ($query) {
                    $query->where('name', 'like', "%{$this->input}%");
                });
            }, 'Filter by Offence')->select(
                Offence::pluck('name', 'name')
            );

            $f->between('court_date', 'Filter by arrest date')->date();
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
                return Utils::my_date($d);
            });
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

        $grid->column('jail_date', 'Jail date')
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
            $actions->disableView();
            $actions->disableEdit();
            $actions->disableDelete();
            $actions->add(new ViewSuspect);
            if ($actions->row->court_status == 'On-going prosecution' || $actions->row->court_status == 'Reinstated') {
                $actions->add(new CourtCaseUpdate);
            }
            // If the user is not a secretary, a CA agent, CA team lead and secretary, then they can edit the court case
            if (
                !Auth::user()->isRole('ca-agent') && !Auth::user()->isRole('ca-team') && !Auth::user()->isRole('secretary') && !Auth::user()->isRole('prosecutor')) {
                $actions->add(new EditCourtCase);
            }
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
                            $form->radio('is_jailed', __('Was Accused jailed?'))
                                ->options([
                                    "Yes" => 'Yes',
                                    "No" => 'No',
                                ])
                                ->when('Yes', function ($form) {
                                    $form->date('jail_date', 'Jail date');
                                    $form->decimal('jail_period', 'Jail period')->help("(In months)");
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
                                    );
                                })
                                ->default('No');


                            $form->radio('cautioned', __('Was Accused cautioned?'))
                                ->options([
                                    'Yes' => 'Yes',
                                    'No' => 'No',
                                ])
                                ->when('Yes', function ($form) {
                                    $form->text('cautioned_remarks', 'Enter caution remarks');
                                })
                                ->default('No');

                            $form->radio('suspect_appealed', __('Did the Accused appeal?'))
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
                            $form->textarea('case_outcome_remarks', 'Remarks');
                        });
                })
                ->when('in', ['On-going investigation', 'On-going prosecution', 'Reinstated'], function ($form) {


                    $form->select('suspect_court_outcome', 'Accused court case status')->options(
                        SuspectCourtStatus::pluck('name', 'name')
                    )->rules('required');
                })
                ->rules('required');
        } else {
            $form->radio('is_suspect_appear_in_court', __('Has this Accused appeared in court?'))
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
                            ])
                                ->rules('required');
                        })
                        ->when('Closed', function ($form) {
                            $form->select('police_action', 'Case outcome at police level')->options([
                                'Dismissed by state' => 'Dismissed by state',
                                'Withdrawn by complainant' => 'Withdrawn by complainant',
                            ]);
                            $form->date('police_action_date', 'Date');
                            $form->textarea('police_action_remarks', 'Remarks');
                        })->when('Re-opened', function ($form) {
                            $form->select('police_action', 'Case outcome at police level')->options([
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
                        $form->text('court_file_number', 'Court file number')
                            ->default($courtFileNumber)
                            ->value($courtFileNumber)
                            ->readonly()
                            ->rules('required');
                    }

                    $form->date('court_date', 'Court Date of first appearance')->rules('required');
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
                                    $form->radio('is_jailed', __('Was Accused jailed?'))
                                        ->options([
                                            "Yes" => 'Yes',
                                            "No" => 'No',
                                        ])
                                        ->when('Yes', function ($form) {
                                            $form->date('jail_date', 'Jail date');
                                            $form->decimal('jail_period', 'Jail period')->help("(In months)");
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
                                            );
                                        })
                                        ->default('No');

                                    $form->radio('cautioned', __('Was Accused cautioned?'))
                                        ->options([
                                            'Yes' => 'Yes',
                                            'No' => 'No',
                                        ])
                                        ->when('Yes', function ($form) {
                                            $form->text('cautioned_remarks', 'Enter caution remarks');
                                        })
                                        ->default('No');

                                    $form->radio('suspect_appealed', __('Did the Accused appeal?'))
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
                                    $form->textarea('case_outcome_remarks', 'Remarks');
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
