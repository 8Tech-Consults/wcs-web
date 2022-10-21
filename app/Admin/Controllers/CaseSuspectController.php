<?php

namespace App\Admin\Controllers;

use App\Models\CaseSuspect;
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

        $form->number('case_id', __('Case id'));
        $form->text('uwa_suspect_number', __('Uwa suspect number'));
        $form->text('first_name', __('First name'));
        $form->text('middle_name', __('Middle name'));
        $form->text('last_name', __('Last name'));
        $form->text('phone_number', __('Phone number'));
        $form->text('national_id_number', __('National id number'));
        $form->text('sex', __('Sex'));
        $form->number('age', __('Age'));
        $form->text('occuptaion', __('Occuptaion'));
        $form->text('country', __('Country'));
        $form->number('district_id', __('District id'));
        $form->number('sub_county_id', __('Sub county id'));
        $form->text('parish', __('Parish'));
        $form->text('village', __('Village'));
        $form->text('ethnicity', __('Ethnicity'));
        $form->textarea('finger_prints', __('Finger prints'));
        $form->switch('is_suspects_arrested', __('Is suspects arrested'));
        $form->datetime('arrest_date_time', __('Arrest date time'))->default(date('Y-m-d H:i:s'));
        $form->number('arrest_district_id', __('Arrest district id'));
        $form->number('arrest_sub_county_id', __('Arrest sub county id'));
        $form->text('arrest_parish', __('Arrest parish'));
        $form->text('arrest_village', __('Arrest village'));
        $form->text('arrest_latitude', __('Arrest latitude'));
        $form->text('arrest_longitude', __('Arrest longitude'));
        $form->text('arrest_first_police_station', __('Arrest first police station'));
        $form->text('arrest_current_police_station', __('Arrest current police station'));
        $form->text('arrest_agency', __('Arrest agency'));
        $form->text('arrest_uwa_unit', __('Arrest uwa unit'));
        $form->text('arrest_detection_method', __('Arrest detection method'));
        $form->text('arrest_uwa_number', __('Arrest uwa number'));
        $form->text('arrest_crb_number', __('Arrest crb number'));
        $form->switch('is_suspect_appear_in_court', __('Is suspect appear in court'));
        $form->text('prosecutor', __('Prosecutor'));
        $form->switch('is_convicted', __('Is convicted'));
        $form->textarea('case_outcome', __('Case outcome'));
        $form->textarea('magistrate_name', __('Magistrate name'));
        $form->textarea('court_name', __('Court name'));
        $form->textarea('court_file_number', __('Court file number'));
        $form->switch('is_jailed', __('Is jailed'));
        $form->number('jail_period', __('Jail period'));
        $form->switch('is_fined', __('Is fined'));
        $form->number('fined_amount', __('Fined amount'));
        $form->number('status', __('Status'));

        return $form;
    }
}
