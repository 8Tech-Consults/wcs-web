<?php

namespace App\Admin\Controllers;

use App\Models\CaseModel;
use App\Models\CaseSuspect;
use App\Models\Location;
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

class CourtsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Court cases';

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
            if ($pendingCase->case_step == 1) {
                return redirect(admin_url('new-case-suspects/create'));
            } else if ($pendingCase->case_step == 2) {
                die("step 2");
            } else if ($pendingCase->case_step == 3) {
            } else {
            }
            //dd($pendingCase);
        }

        if (Utils::hasPendingCase(Auth::user()) != null) {
            // return redirect(admin_url('case-suspects/create'));
        }





        $grid = new Grid(new CaseSuspect());


        $grid->export(function ($export) {

            $export->filename('Cases.csv');

            $export->except(['actions']);

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



        $grid->export(function ($export) {

            $export->filename('Cases.csv');

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
        });



        $grid = new Grid(new CaseSuspect());
        $grid->disableBatchActions();
        $grid->disableCreateButton();
        $grid->disableActions();



        $grid->model()
            ->where([
                'is_suspect_appear_in_court' => 1
            ])->orderBy('id', 'Desc');

        $u = Auth::user();
        if ($u->isRole('ca-agent')) {
            $grid->model()->where([
                'reported_by' => $u->id
            ]);
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

            $f->equal('case_id', 'Filter by offence')->select(function ($id) {
                $a = CaseModel::find($id);
                if ($a) {
                    return [$a->id => "#" . $a->id . " - " . $a->title];
                }
            })
                ->ajax($ajax_url);

            $f->between('court_date', 'Filter by arrest date')->date();
            $f->like('court_name', 'Filter by court name');
            $f->like('court_file_number', 'Filter by court file number');
            $f->like('prosecutor', 'Filter by prosecutor');
            $f->like('magistrate_name', 'Filter by magistrate');
        });


        $grid->model()->orderBy('id', 'Desc');
        $grid->quickSearch('first_name')->placeholder('Search by first name..');

        $grid->column('ca_id', __('CA'))
            ->display(function () {
                if ($this->ca == null) {
                    return  "-";
                }
                return $this->ca->name;
            })
            ->sortable();


        $grid->model()->orderBy('id', 'Desc');


        $grid->model()->orderBy('id', 'Desc');
        $grid->quickSearch('first_name')->placeholder('Search by first name..');

        $grid->column('id', __('ID'))->sortable()->hide();
        $grid->column('created_at', __('Date'))->hide()
            ->display(function ($x) {
                return Utils::my_date_time($x);
            })
            ->sortable();

        $grid->column('suspect_number', __('Suspect number'))
            ->sortable();

        $grid->column('photo_url', __('Photo'))
            ->width(60)
            ->lightbox(['width' => 60, 'height' => 80]);
        $grid->column('updated_at', __('Updated'))
            ->display(function ($x) {
                return Utils::my_date_time($x);
            })
            ->sortable()->hide();

        $grid->column('first_name', __('Name'))
            ->display(function ($x) {
                return $this->first_name . " " . $this->middle_name . " " . $this->last_name;
            })
            ->sortable();


        $grid->column('sex', __('Sex'))
            ->filter([
                'Male' => 'Male',
                'Female' => 'Female',
            ])->hide()
            ->sortable();
        $grid->column('national_id_number', __('NIN'))->hide();
        $grid->column('phone_number', __('Phone number'))->hide();
        $grid->column('occuptaion', __('Occupataion'))->hide();
        $grid->column('country', __('Country'))->hide()->sortable();
        $grid->column('district_id', __('District'))->display(function () {
            return $this->district->name;
        })->hide()->sortable();

        $grid->column('case_id', __('Case'))
            ->display(function ($x) {
                return $this->case->title;
            })
            ->sortable();



        $grid->column('ethnicity')->hide()->sortable();
        $grid->column('parish')->hide()->sortable();
        $grid->column('village')->hide()->sortable();
        $grid->column('is_suspects_arrested', 'Is arrested')
            ->using([
                1 => 'Arrested',
                0 => 'Not arrested',
            ])
            ->hide()
            ->sortable();
        $grid->column('arrest_date_time', 'Arrest date')
            ->display(function ($d) {
                return Utils::my_date($d);
            });
        $grid->column('arrest_district_id', __('District'))
            ->hide()
            ->display(function ($x) {
                return Utils::get('App\Models\Location', $this->arrest_district_id)->name;
            })
            ->sortable();

        $grid->column('arrest_sub_county_id', __('Arrest Sub-county'))
            ->hide()
            ->display(function ($x) {
                return Utils::get(Location::class, $this->arrest_sub_county_id)->name;
            })
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
            ->sortable();
        $grid->column('court_date', 'Court date')

            ->display(function ($d) {
                return Utils::my_date($d);
            });

        $grid->column('prosecutor')->sortable();

        $grid->column('is_convicted', __('Is convicted'))
            ->display(function ($x) {
                if ($x) {
                    return 'Convicted';
                } else {
                    return 'Not convicted';
                }
            })
            ->sortable();

        $grid->column('case_outcome', 'Court outcome')->sortable();
        $grid->column('magistrate_name')->sortable();
        $grid->column('court_name')->sortable();
        $grid->column('court_file_number')->sortable();


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

        $grid->column('jail_period')->sortable();
        $grid->column('is_fined', 'Is fined')
            ->using([
                1 => 'Fined',
                0 => 'Not fined',
            ])
            ->sortable();
        $grid->column('fined_amount')->sortable();
        $grid->column('management_action')->hide()->sortable();
        $grid->column('community_service')->hide()->sortable();

        $grid->column('reported_by', __('Reported by'))
            ->display(function () {

                return $this->case->reportor->name;
            })->hide()
            ->sortable();
        $grid->actions(function ($actions) {
            $actions->disableDelete();
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




    /*     protected function grid()
    {

        $grid = new Grid(new CaseSuspect());
        $grid->disableBatchActions();
        $grid->disableCreateButton();
        $grid->disableActions();

        $grid->model()
            ->where([
                'is_suspect_appear_in_court' => 1
            ])->orderBy('id', 'Desc');

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

            $f->equal('case_id', 'Filter by offence')->select(function ($id) {
                $a = CaseModel::find($id);
                if ($a) {
                    return [$a->id => "#" . $a->id . " - " . $a->title];
                }
            })
                ->ajax($ajax_url);

            $f->between('court_date', 'Filter by arrest date')->date();
            $f->like('court_name', 'Filter by court name');
            $f->like('court_file_number', 'Filter by court file number');
            $f->like('prosecutor', 'Filter by prosecutor');
            $f->like('magistrate_name', 'Filter by magistrate');
        });


        $grid->model()->orderBy('id', 'Desc');
        $grid->quickSearch('first_name')->placeholder('Search by first name..');

        $grid->column('id', __('Suspect ID'))->sortable();
        $grid->column('created_at', __('Reported'))
            ->display(function ($x) {
                return Utils::my_date_time($x);
            })
            ->hide()
            ->sortable();


        $grid->column('photo_url', __('Photo'))
            ->width(60)
            ->lightbox(['width' => 60, 'height' => 80]);
        $grid->column('updated_at', __('Updated'))
            ->display(function ($x) {
                return Utils::my_date_time($x);
            })
            ->sortable()->hide();



        $grid->column('first_name', __('Name'))
            ->display(function ($x) {
                return $this->first_name . " " . $this->middle_name . " " . $this->last_name;
            })
            ->sortable();
        $grid->column('case_id', __('Offence'))
            ->display(function ($x) {
                return $this->case->title;
            })
            ->sortable();


        $grid->column('is_suspect_appear_in_court', __('Court status'))
            ->sortable()
            ->using([
                0 => 'Not in court',
                1 => 'In court',
            ], 'Not arrested')->label([
                null => 'danger',
                0 => 'danger',
                1 => 'success',
            ], 'danger');

        $grid->column('court_date', __('Court date'))
            ->display(function ($x) {
                return Utils::my_date($x);
            })
            ->sortable();

        $grid->column('court_name', __('Court name'))
            ->sortable();

        $grid->column('court_file_number', __('Court file number'))
            ->sortable();

        $grid->column('prosecutor', __('Prosecutor'))
            ->sortable();

        $grid->column('magistrate_name', __('Magistrate name'))
            ->sortable();




        $grid->column('case_outcome', __('Case outcome'))
            ->sortable();
        $grid->column('action', __('Actions'))->display(function () {

            $view_link = '<a class="" href="' . url("case-suspects/{$this->id}") . '">
            <i class="fa fa-eye"></i>View</a>';
            $edit_link = '<br><br><a class="" href="' . url("case-suspects/{$this->id}/edit") . '">
            <i class="fa fa-edit"></i> Edit</a>';
            return $view_link . $edit_link;
        });
        $grid->actions(function ($actions) {
            $actions->disableDelete();
        });
        return $grid;
    } */
}
