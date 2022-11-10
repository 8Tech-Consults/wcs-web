<?php

namespace App\Admin\Controllers;

use App\Models\CaseModel;
use App\Models\Location;
use App\Models\PA;
use App\Models\Utils;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Widgets\InfoBox;
use Faker\Factory as Faker;

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

        /*
        $faker = Faker::create();
        $admins = Administrator::all()->pluck('id');
        $_admins = [0, 1, 2, 4, 5, 6, 7, 8];
        $sub_counties =  [];
        $statuses =  [true, false];
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
        foreach (Location::get_sub_counties() as $v) {
            $sub_counties[] = $v->id;
        }

        for ($i = 0; $i < 49; $i++) {
            shuffle($_admins);
            shuffle($sub_counties);
            shuffle($parishes);
            shuffle($statuses);
            shuffle($titles);
            $c = new CaseModel();
            $c->reported_by = $admins[$_admins[2]];
            $c->latitude = '0.615085';
            $c->longitude = '30.391306';
            $c->sub_county_id = $sub_counties[2];
            $c->parish = $parishes[2];
            $c->village = $parishes[3];
            $c->offence_category_id = 1;
            $c->offence_description = $faker->sentence(100);
            $c->offence_description .= "<br>" . $faker->sentence(100);
            $c->offence_description .= "<br>" . $faker->sentence(100);
            $c->is_offence_committed_in_pa = 0;
            $c->pa_id = 1;
            $c->has_exhibits = 1;
            $c->status = $statuses[0];
            $c->title = $titles[2];
            $c->save();
        }*/



        $grid = new Grid(new CaseModel());
        $grid->disableBatchActions();
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            $actions->disableDelete();
        });


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

        $grid->column('reported_by', __('Reported by'))
            ->display(function () {
                return $this->reportor->name;
            });
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
            return count($this->suspects);
        });
        $grid->column('exhibits', __('Exhibits'))->display(function () {
            return count($this->suspects);
        });
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
            ], 'danger')
            ->filter([
                0 => 'Pending',
                1 => 'Active',
                2 => 'Closed',
            ]);

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


        if ($form->isCreating()) {
            $form->hidden('reported_by', __('Reported by'))->default(Admin::user()->id)->rules('int|required');
        }

        $form->tab('BASIC INFORMATION', function ($form) {

            $form->text('title', __('Case title'))
                ->help("Describe this case in summary")
                ->rules('required');
            $form->radio('location_picker', __('Case location'))
                ->options([
                    1 => 'Pick my current location',
                    2 => 'Pick from map',
                ])
                ->help("GPS Location where the case took place")
                ->when(1, function (Form $form) {

                    $form->html('
                <a id="location-picker" href="javascript:;" class="btn btn-info btn-lg">PICK MY GPS LOCATION</a>');

                    $form->text('latitude', __('GPS latitude'))
                        ->rules('required');
                    $form->text('longitude', __('GPS longitude'))
                        ->rules('required');
                })
                ->when(2, function (Form $form) {
                    /*  $form->latlong('latitude', 'longitude', 'Position')->height(500)->rules('required'); */
                })
                ->rules('required');




            $form->select('sub_county_id', __('Sub county'))
                ->rules('int|required')
                ->options(Location::get_sub_counties()->pluck('name_text', 'id'));
            $form->text('parish', __('Parish'))->rules('required');
            $form->text('village', __('Village'))->rules('required');


            $form->select('offence_category_id', __('Offence category'))
                ->rules('int|required')
                ->options([
                    1 => 'Type 1',
                    2 => 'Type 2',
                    3 => 'Type 3',
                    4 => 'Type 4',
                ]);



            $form->textarea('offence_description', __('Offence description'))->rules('required');


            $form->radio('is_offence_committed_in_pa', __('Is offence committed within a PA?'))
                ->rules('int|required')
                ->options([
                    1 => 'Yes',
                    0 => 'No',
                ])
                ->default(0)
                ->when(1, function (Form $form) {

                    $form->select('pa_id', __('Select PA'))
                        ->rules('int|required')
                        ->options(PA::all()->pluck('name_text', 'id'));
                });


            $form->radio('has_exhibits', __('Does this case have exhibits?'))
                ->rules('int|required')
                ->options([
                    1 => 'Yes',
                    0 => 'No',
                ]);


            if ($form->isCreating()) {
                $form->select('status', __('Status'))
                    ->options([
                        1 => 'Save as draft',
                        2 => 'Submit case for approval',
                        0 => 'No',
                    ])
                    ->default(1);
            }
        });

        $form->tab('EXHIBITS', function ($form) {

            $form->html('Click on new to add an exhibit');
            $form->morphMany('exhibits', null, function (Form\NestedForm $form) {
                /* 					 */
                $form->select('exhibit_catgory', __('Exhibit catgory'))
                    ->options([
                        'Wildlife' => 'Wildlife',
                        'Implements' => 'Implements',
                    ])
                    ->rules('required');
                $form->decimal('quantity')
                    ->help('(in KGs)')
                    ->rules('int|required');
                $form->image('photos');
                $form->textarea('description')
                    ->help('Explain more about this exhibit')
                    ->rules('required');
            });
        });



        /* Admin::css(".help-block{padding: 0px!important; margin: 0px!important; } "); */
        $form->tab('SUSPECTS', function ($form) {

            $form->html('Click on new to add a suspect');
            $form->morphMany('suspects', null, function (Form\NestedForm $form) {

                $form->text('uwa_suspect_number')->rules('required');
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
                $form->text('occuptaion')->rules('required');
                $form->select('country')
                    ->help('Nationality of the suspect')
                    ->options(Utils::COUNTRIES())->rules('required');

                $form->select('sub_county_id', __('Sub county'))
                    ->rules('int|required')
                    ->help('Where this suspect originally lives')
                    ->options(Location::get_sub_counties()->pluck('name_text', 'id'));

                $form->text('parish');
                $form->text('village');
                $form->text('ethnicity');
                $form->text('finger_prints');
                $form->radio('is_suspects_arrested', "Is this suspect arreseted?")
                    ->options([
                        1 => 'Yes',
                        0 => 'No',
                    ])
                    ->rules('required');
                $form->datetime('arrest_date_time', 'Arrest date and time');

                $form->select('arrest_sub_county_id', __('Arrest Sub county'))
                    ->rules('int|required')
                    ->help('Where this suspect was arrested')
                    ->options(Location::get_sub_counties()->pluck('name_text', 'id'));

                $form->text('arrest_parish', 'Arrest parish');
                $form->text('arrest_village', 'Arrest vaillage');

                $form->latlong('arrest_latitude', 'arrest_longitude', 'Arrest location on map')->height(500)->rules('required');
                $form->text('arrest_first_police_station', 'Arrest police station');
                $form->text('arrest_current_police_station', 'Current police station');
                $form->text('arrest_agency', 'Arrest agency');
                $form->text('arrest_uwa_unit', 'UWA Unit');
                $form->text('arrest_detection_method', 'Arrest detection method');
                $form->text('arrest_uwa_number', 'UWA Arest number');
                $form->text('arrest_crb_number', 'CRB number');

                $form->radio('is_suspect_appear_in_court', __('Has this suspect appeared in court?'))
                    ->options([
                        1 => 'Yes',
                        0 => 'No',
                    ]);
                $form->text('prosecutor', 'Names of the prosecutors');
                $form->radio('is_convicted', __('Has suspect been convicted?'))
                    ->options([
                        1 => 'Yes',
                        0 => 'No',
                    ]);

                $form->text('case_outcome', 'Case outcome');
                $form->text('magistrate_name', 'Magistrate Name');
                $form->text('court_name', 'Court Name');
                $form->text('court_file_number', 'Court file number');

                $form->radio('is_jailed', __('Has suspect been jailed?'))
                    ->options([
                        1 => 'Yes',
                        0 => 'No',
                    ]);
                $form->decimal('jail_period', 'Jail period')->help("(In months)");
                $form->radio('is_fined', __('Has suspect been fined?'))
                    ->options([
                        1 => 'Yes',
                        0 => 'No',
                    ]);

                $form->decimal('fined_amount', 'File amount')->help("(In UGX)");

                $form->select('status', __('Status'))
                    ->options([
                        1 => 'Not arrested',
                        2 => 'Arrested',
                        2 => 'Other status',
                        0 => 'No',
                    ])
                    ->default(1);
            });
        });



        Admin::js(url('js/form-drug-sellers.js'));
        return $form;
    }
}
