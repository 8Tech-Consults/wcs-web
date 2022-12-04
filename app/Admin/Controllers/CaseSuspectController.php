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

        if (Utils::hasPendingCase(Auth::user()) != null) {
            return redirect(admin_url('case-suspects/create'));
        }

        /*  foreach (CaseSuspect::all() as $key => $s) {
            $s->photo = ((rand(1000,10000)%20)+1) .".jpg";
            $s->save();
        }   */
        /*

        $faker = Faker::create();
        $admins = Administrator::all()->pluck('id');
        $_admins = [0, 1, 2, 4, 5, 6, 7, 8];
        $sub_counties =  [];
        $statuses =  [true, false];
        $sex =  ['Male', 'Female'];
        $countries =  Utils::COUNTRIES();
        $titles =  [
            'Found with 3 pairs of rhino tails.',
            'Killing of 6 lions and 2 elephants.',
            'Killed hippopotamus.',
            'Found with 20 live pangolins.',
            'Found with 11 crowned crane birds.',
        ];

        $parishes =  [
            'Kinoni', 'Ntusi', 'Lwemiyaga', 'Kyankoko', 'Mugore', 'Lwebisya', 'Lyantonde', 'Kiruhura', 'Sembabule',
            'Adumi', 'Ajia', 'Arivu', 'Aroi', 'Arua Hill', 'Dadamu', 'Logiri', 'Manibe', 'Offaka'
        ];
        $ethnicity =  [
            'Musoga', 'Mugishu', 'Mukonzo', 'Muganda', 'Munyankole', 'Mucholi', 'Muchiga'
        ];
        foreach (Location::get_sub_counties() as $v) {
            $sub_counties[] = $v->id;
        }

        $cases = [];
        foreach (CaseModel::all() as $v) {
            $cases[] = $v->id;
        }

        for ($i = 0; $i < 1000; $i++) {
            shuffle($_admins);
            shuffle($sub_counties);
            shuffle($parishes);
            shuffle($statuses);
            shuffle($titles);
            shuffle($cases);
            shuffle($sex);
            shuffle($ethnicity);
            shuffle($countries);
            $s = new CaseSuspect();
            $s->case_id = $cases[2];
            $s->uwa_suspect_number =  "UG-" . $faker->randomNumber(5, false);
            $s->arrest_uwa_number =  "AR-" . $faker->randomNumber(5, false);
            $s->first_name =   $faker->firstName(0);
            $s->last_name =   $faker->lastName(0);
            $s->middle_name =   '';
            $s->phone_number =   $faker->e164PhoneNumber;
            $s->national_id_number =   $faker->numberBetween(100000000000000, 1000000000000000);
            $s->arrest_crb_number =   $faker->numberBetween(1000000000, 10000000000);
            $s->court_file_number =   $faker->numberBetween(1000000, 100000000);
            $s->sex =   $sex[0];
            $s->age = $faker->date();
            $s->occuptaion = $faker->jobTitle();
            $s->country =   $countries[0];
            $s->sub_county_id =   $sub_counties[0];
            $s->parish =   $parishes[0];
            $s->village =   $parishes[2];
            $s->ethnicity =   $ethnicity[2];
            $s->finger_prints =  '';
            $s->is_suspects_arrested =  false;
            $s->is_suspect_appear_in_court =  false;
            $s->arrest_date_time = $faker->date();
            $s->arrest_sub_county_id =   $sub_counties[0];
            $s->arrest_parish =   $parishes[2];
            $s->arrest_village =   $parishes[2];
            $s->prosecutor =   $faker->name();
            $s->arrest_first_police_station =   $parishes[2] . " Police post";
            $s->arrest_current_police_station =   $parishes[2] . " Police post";
            $s->arrest_agency =   $parishes[2] . " Police post";
            $s->court_name =   $parishes[2] . " Court";
            $s->arrest_latitude =   '0.615085';
            $s->arrest_longitude =   '30.391306';
            $s->arrest_uwa_unit =   '-';
            $s->arrest_detection_method =   'Prison cell';
            $s->is_convicted =  false;
            $s->case_outcome =  "Charged";
            $s->magistrate_name =   $faker->name();
            $s->is_jailed =  $statuses[0];
            shuffle($statuses);
            $s->jail_period =  $statuses[0];
            shuffle($statuses);
            $s->jail_period =  $statuses[0];
            shuffle($statuses);
            $s->is_fined =  $statuses[0];
            shuffle($statuses);
            $s->fined_amount =  $statuses[0];
            shuffle($statuses);
            $s->status =  $statuses[0];

            $s->save();
        }*/



        $grid = new Grid(new CaseSuspect());
        $grid->disableBatchActions();
        $grid->disableActions();


        $grid->model()
            ->where(
                'is_suspects_arrested',
                '!=',
                1
            )
            ->where(
                'is_suspect_appear_in_court',
                '!=',
                1
            )
            ->where(
                'is_jailed',
                '!=',
                1
            )
            ->orderBy('created_at', 'Desc');



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

            $f->equal('is_convicted', 'Filter by conviction status')->select([
                0 => 'Not Convicted',
                1 => 'Convicted',
            ]);

            $f->equal('is_jailed', 'Filter by jail status')->select([
                0 => 'Not jailed',
                1 => 'Jailed',
            ]);
        });




        $grid->model()->orderBy('id', 'Desc');
        $grid->quickSearch('first_name')->placeholder('Search by first name..');

        $grid->column('id', __('ID'))->sortable()->hide();
        $grid->column('created_at', __('Date'))
            ->display(function ($x) {
                return Utils::my_date_time($x);
            })
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
            ])
            ->sortable();
        $grid->column('national_id_number', __('NIN'));
        $grid->column('phone_number', __('Phone number'));
        $grid->column('uwa_suspect_number', __('UWA suspect number'))->sortable();
        $grid->column('occuptaion', __('Occuptaion'));
        $grid->column('country', __('Country'))->sortable();
        $grid->column('district_id', __('District'))->display(function () {
            return $this->district->name;
        })->sortable();

        $grid->column('case_id', __('Case'))
            ->display(function ($x) {
                return $this->case->title;
            })
            ->sortable();


        $grid->column('status', __('Interest'))
            ->sortable()
            ->using([
                1 => 'Case of interest',
                0 => 'NOT case of interest',
            ], 'Not in Court')->dot([
                null => 'danger',
                1 => 'danger',
                0 => 'success',
            ], 'danger')
            ->filter([
                1 => 'Case of interest',
                0 => 'NOT case of interest',
            ]);

        $grid->column('is_convicted', __('Convicted'))
            ->sortable()
            ->using([
                0 => 'Not Convicted',
                1 => 'Convicted',
            ],)->label([
                null => 'danger',
                0 => 'danger',
                1 => 'success',
            ], 'danger');

        $grid->column('is_jailed', __('Jailed'))
            ->sortable()
            ->using([
                0 => 'Not Jailed',
                1 => 'Jailed',
            ],)->label([
                null => 'danger',
                0 => 'danger',
                1 => 'success',
            ], 'danger');

        $grid->column('action', __('Actions'))->display(function () {

            $view_link = '<a class="" href="' . url("case-suspects/{$this->id}") . '">
            <i class="fa fa-eye"></i>View</a>';
            $edit_link = '<br> <a class="" href="' . url("case-suspects/{$this->id}/edit") . '">
            <i class="fa fa-edit"></i> Edit</a>';
            return $view_link . $edit_link;
        });
        $grid->actions(function ($actions) {
            $actions->disableDelete();
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
        $show->field('occuptaion', __('Occuptaion'));
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







        return $form;
    }
}
