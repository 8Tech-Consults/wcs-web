<?php

namespace App\Admin\Controllers;

use App\Models\CaseSuspect;
use App\Models\Location;
use App\Models\Utils;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CaseSuspectController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'CaseSuspect';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
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
        $grid->column('occuptaion', __('Occuptaion'));
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
        $form = new Form(new CaseSuspect());


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

        return $form;
    }
}
