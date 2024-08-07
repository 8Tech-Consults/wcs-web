<?php

namespace App\Admin\Controllers;

use App\Models\CaseModel;
use App\Models\ConservationArea;
use App\Models\Location;
use App\Models\Offence;
use App\Models\PA;
use App\Models\DetectionMethod;
use App\Models\Utils;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Auth;

class NewCaseModelController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Creating new case - case information';

    /**
     * Make a grid builder. 
     *
     * @return Grid
     */
    protected function grid()
    {

        if (isset($_GET['remove_case'])) {
            $id = ((int)($_GET['remove_case']));
            return '<div class="bg-light p-4 p-md-5" >
                <h4 class="">Are you sure you want to cancel?</h4>
                <p>Canceling this case creation will delete all information that you had entered about it.</p>
                <p><a class="btn btn-danger" href="' . admin_url('new-case?do_remove_case=' . $id) . '">CANCEL & DELETE CASE</a> | <a class="btn btn-success" href="' . admin_url('cases') . '">CONTINUE CREATING CASE</a></p>
            </div>';
        }

        if (isset($_GET['do_remove_case'])) {
            $id = ((int)($_GET['do_remove_case']));
            $case = CaseModel::find($id);

            if ($case != null) {
                $case->delete();
                Admin::script('window.location.replace("' . admin_url('cases') . '");');
                return 'Loading...';
            }
        }


        $pendingCase = Utils::hasPendingCase(Auth::user());
        if ($pendingCase != null) {
            Admin::script('window.location.replace("' . admin_url('new-case-suspects/create') . '");');
            return 'Loading...';
            //dd($pendingCase); 
        }
        Admin::script('window.location.replace("' . admin_url('new-case/create') . '");');
        return 'Loading...';

        $grid = new Grid(new CaseModel());

        $grid->column('id', __('Id'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        $grid->column('reported_by', __('Reported by'));
        $grid->column('latitude', __('Latitude'));
        $grid->column('longitude', __('Longitude'));
        $grid->column('district_id', __('District id'));
        $grid->column('sub_county_id', __('Sub county id'));
        $grid->column('parish', __('Parish'));
        $grid->column('village', __('Village'));
        $grid->column('offence_category_id', __('Offence category id'));
        $grid->column('offence_description', __('Offence description'));
        $grid->column('is_offence_committed_in_pa', __('Is offence committed in pa'));
        $grid->column('pa_id', __('Pa id'));
        $grid->column('location', __('Location'));
        $grid->column('has_exhibits', __('Has exhibits'));
        $grid->column('status', __('Status'));
        $grid->column('title', __('Title'));
        $grid->column('location_picker', __('Location picker'));
        $grid->column('deleted_at', __('Deleted at'));
        $grid->column('case_number', __('Case number'));
        $grid->column('done_adding_suspects', __('Done adding suspects'));
        $grid->column('ca_id', __('Ca id'));
        $grid->column('detection_method', __('Detection method'));
        $grid->column('ca_id', __('Conservation area id'));
        $grid->column('offense_category', __('Offense category'));

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
                Admin::script('window.location.replace("' . admin_url('new-case-suspects/create') . '");');
                return 'Loading...';
            } else if ($pendingCase->case_step == 2) {
            } else if ($pendingCase->case_step == 3) {
            } else {
            }
        }

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
        $show->field('location', __('Location'));
        $show->field('has_exhibits', __('Has exhibits'));
        $show->field('status', __('Status'));
        $show->field('title', __('Title'));
        $show->field('location_picker', __('Location picker'));
        $show->field('deleted_at', __('Deleted at'));
        $show->field('case_number', __('Case number'));
        $show->field('done_adding_suspects', __('Done adding suspects'));
        $show->field('ca_id', __('Ca id'));
        $show->field('detection_method', __('Detection method'));
        $show->field('ca_id', __('Conservation area id'));
        $show->field('offense_category', __('Offense category'));

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

        if (isset($_GET['refresh_page'])) {
            $r = (int)($_GET['refresh_page']);
            if ($r == 1) {
                Admin::script('window.location.replace("' . admin_url('new-case/create') . '");');
                return 'Loading...';
            }
        
        }

        $form->saved(function (Form $form) {
            $pendingCase = Utils::hasPendingCase(Auth::user());
            if ($pendingCase != null) {
                if ($pendingCase->case_step == 1) {
                    Admin::script('window.location.replace("' . admin_url('new-case-suspects/create') . '");');
                    return 'Loading...';
                }
            }
        });

        if ($form->isCreating()) {
            $pendingCase = Utils::hasPendingCase(Auth::user());
            if ($pendingCase != null) {
                if ($pendingCase->case_step == 1) {
                    Admin::script('window.location.replace("' . admin_url('new-case-suspects/create') . '");');
                    return 'Loading...';
                }
            }else {
                Admin::css('https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css');
                Admin::js('https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js');
                
                Admin::script("
                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'To avoid double entry, please check if suspect(s) has already been reported.',
                        icon: 'warning',
                        showCancelButton: true,
                        allowOutsideClick: false,
                        buttonsStyling: false,
                        confirmButtonText: 'Yes, check',
                        cancelButtonText: 'No, Proceed',
                        customClass: {
                            confirmButton: 'btn fw-bold btn-active-light-primary',
                            cancelButton: 'btn fw-bold btn-danger ml-5',
                        }
                    }).then(function (result) {
                        if (result.value) {
                            window.location.replace('/case-suspects');
                            return 'Loading...';
                        }
                    })
                ");
            }
        }


        Admin::css(url('/css/new-case.css'));
        $pendingCase = Utils::hasPendingCase(Auth::user());

        $form->disableCreatingCheck();
        $form->disableReset();
        $form->disableEditingCheck();
        $form->disableViewCheck();

        $form->html(view('steps', ['case' => $pendingCase]));

        $form->hidden('reported_by', __('Reported by'))->default(Admin::user()->id)->rules('required');
        $form->hidden('case_submitted', __('Reported by'))->default(0);

        if ($form->isEditing()) {
            $form->html('<a class="btn btn-danger" href="' . admin_url("new-case-suspects/create") . '" >SKIP TO SUSPECTS</a>', 'SKIP');
        }

        $form->text('title', __('Case title:  Uganda Vs '))
            ->help("Enter suspects names here")
            ->placeholder("Enter suspects names here")
            ->rules('required');
            
        $form->textarea('offence_description', __('Case description'))
            ->help("Describe this case in details");

        $form->date('case_date', 'Date when happened')
            ->rules('required|before_or_equal:today');

        $form->text('officer_in_charge', 'Complainant')->rules('required');


        $form->radio('is_offence_committed_in_pa', __('Did the case take place in a PA?'))
            ->options([
                'Yes' => 'Yes',
                'No' => 'No',
            ])
            ->when('No', function (Form $form) {


                $form->select('sub_county_id', __('Sub county'))
                    ->rules('required')
                    ->options(Location::get_sub_counties_array());

                $form->text('parish', __('Parish'))->rules('required');
                $form->text('village', __('Village'))->rules('required');
            })
            ->when('Yes', function (Form $form) {
                $form->select('pa_id', __('Select PA'))
                    ->rules('required')
                    ->options(PA::where('id', '!=', 1)->get()
                        ->pluck('name_text', 'id'));
                $form->text('village', 'Enter location')->rules('required');
            })
            ->rules('required');

        $form->text('latitude', 'Case scene GPS - latitude');
        $form->text('longitude', 'Case scene GPS - longitude');


        $form->hidden('has_exhibits', __('Does this case have exhibits?'))
            ->default(1);

        $form->select('detection_method', __('Detection method'))
            ->options(
                DetectionMethod::pluck('name', 'name'))
            ->rules('required');

            $form->saving(function (Form $form) {
                if($form->isCreating()){
                    $form->title = "Uganda Vs " . $form->title;
                }
            });

        return $form;
    }
}
