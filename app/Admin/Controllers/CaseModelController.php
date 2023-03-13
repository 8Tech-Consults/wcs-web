<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\CaseModel\CaseModelActionAddSuspect;
use App\Models\CaseModel;
use App\Models\CaseSuspect;
use App\Models\ConservationArea;
use App\Models\Location;
use App\Models\Offence;
use App\Models\PA;
use App\Models\TempData;
use App\Models\Utils;
use Carbon\Carbon;
use Encore\Admin\Actions\RowAction;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Widgets\InfoBox;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Auth;


class AddSuspectAction extends RowAction
{
    public $name = 'Add suspect';



    public function handle(Model $model)
    {
        return $this->response()->success('Success!')->redirect('/admin/users');
        // Here the model's `replicate` method is called to copy the data, then call the `save` method to save it.
        $model->replicate()->save();

        // return a success message with the content "copy success" and refresh the page
        return $this->response()->success('copy success.')->refresh();
    }
}

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

      

        $pendingCase = Utils::hasPendingCase(Auth::user());
        if ($pendingCase != null) {
            Admin::script('window.location.replace("' . admin_url('new-case-suspects/create') . '");');
            return 'Loading...';
        }


        $grid = new Grid(new CaseModel()); 
        $grid->disableCreateButton();

        $grid->model()->orderBy('id', 'Desc');


        $u = Auth::user();
        if ($u->isRole('ca-agent')) {
            $grid->model()->where([
                'reported_by' => $u->id
            ]);
            $grid->disableExport();
        } else if (
            $u->isRole('ca-team')
        ) {
            $grid->model()->where([
                'ca_id' => $u->ca_id
            ])->orWhere([
                'reported_by' => $u->id
            ]);
        }

        //if($u->isRole('admin'))


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





        $grid->filter(function ($f) {
            // Remove the default id filter
            $f->disableIdFilter();
            $f->between('created_at', 'Filter by date')->date();
            $f->equal('reported_by', "Filter by reporter")
                ->select(Administrator::all()->pluck('name', 'id'));

            $ajax_url = url(
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
                ->ajax($ajax_url);


            $f->equal('status', 'Filter case status')->select([
                0 => 'Pending',
                1 => 'Active',
                2 => 'Closed',
            ]);
        });



        $grid->disableBatchActions();
        //$grid->disableActions();

        $grid->actions(function ($actions) {

            if (
                Auth::user()->isRole('hq-team-leaders') ||
                Auth::user()->isRole('ca-team')
            ) {
                $actions->disableEdit();
            }
            $actions->disableDelete();
            $actions->add(new CaseModelActionAddSuspect);
        });



        $grid->quickSearch('title')->placeholder("Search by case title...");


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

        $grid->column('case_number', __('Case number'))
            ->sortable();

        $grid->column('title', __('Title'))
            ->sortable();


        $grid->column('ca_id', __('CA'))
            ->display(function () {
                if ($this->ca == null) {
                    return  "-";
                }
                return $this->ca->name;
            })
            ->sortable();
        $grid->column('district_id', __('District'))
            ->hide()
            ->display(function () {
                return $this->district->name;
            })
            ->sortable();

        $grid->column('sub_county_id', __('Sub-county'))
            ->hide()
            ->display(function () {
                return $this->sub_county->name;
            })
            ->sortable();


        $grid->column('suspects_count', __('Suspects'))->display(function () {
            $link = admin_url('case-suspects', ['case_id' => $this->id]);
            return '<a data-toggle="tooltip" data-placement="bottom"  title="View suspects" class="text-primary h3" href="' . $link . '" >' . $this->suspects_count . '</a>';
        });
        $grid->column('exhibit_count', __('Exhibits'))->display(function () {
            $link = admin_url('exhibits');
            return '<a data-toggle="tooltip" data-placement="bottom"  title="View exhibits" class="text-primary h3" href="' . $link . '" >' . $this->exhibit_count . '</a>';
        });

        $grid->column('reported_by', __('Reported by'))
            ->display(function () {
                return $this->reportor->name;
            })
            ->sortable();
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
            ], 'danger');


        /*  $grid->column('actions', __('Actions'))->display(function () {
            $view_link = '<a class="" href="' . url("cases/{$this->id}") . '">
                <i class="fa fa-eye"></i> View case details</a>';

            $suspetcs_link = '<br><a class="" href="' . url("case-suspects?case_id={$this->id}") . '">
                <i class="fa fa-users"></i> View case suspetcs</a>';
            $suspetcs_link = "";

            $edit_link = '<br> <a class="" href="' . url("cases/{$this->id}/edit") . '">
                <i class="fa fa-edit"></i> Edit case info</a>';

            $add_link = '<br> <a class="" href="' . url("case-suspects/create?case_id={$this->id}") . '">
                <i class="fa fa-user-plus"></i> Add case suspect</a>';

            return $view_link . $suspetcs_link . $edit_link . $add_link;
        }); */
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



        /* 
        
        */
        $form = new Form(new CaseModel());


        $form->ignore('temp_want_to_add_suspect', 'want_to_add_suspect');
        $form->ignore('temp_first_name', 'first_name');
        $form->ignore('temp_middle_name', 'middle_name');
        $form->ignore('temp_last_name', 'last_name');
        $form->ignore('temp_phone_number', 'phone_number');
        $form->ignore('temp_national_id_number', 'national_id_number');
        $form->ignore('temp_sex', 'sex');
        $form->ignore('temp_age', 'age');
        $form->ignore('temp_occuptaion', 'occuptaion');
        $form->ignore('temp_country', 'country');
        $form->ignore('temp_district_id', 'district_id');
        $form->ignore('temp_sub_county_id', 'sub_county_id');
        $form->ignore('temp_parish', 'parish');
        $form->ignore('temp_village', 'village');
        $form->ignore('temp_ethnicity', 'ethnicity');
        $form->ignore('temp_finger_prints', 'finger_prints');
        $form->ignore('temp_is_suspects_arrested', 'is_suspects_arrested');
        $form->ignore('temp_arrest_date_time', 'arrest_date_time');
        $form->ignore('temp_arrest_district_id', 'arrest_district_id');
        $form->ignore('temp_arrest_sub_county_id', 'arrest_sub_county_id');
        $form->ignore('temp_arrest_parish', 'arrest_parish');
        $form->ignore('temp_arrest_village', 'arrest_village');
        $form->ignore('temp_arrest_latitude', 'arrest_latitude');
        $form->ignore('temp_arrest_first_police_station', 'arrest_first_police_station');
        $form->ignore('temp_arrest_longitude', 'arrest_longitude');
        $form->ignore('temp_arrest_current_police_station', 'arrest_current_police_station');
        $form->ignore('temp_arrest_agency', 'arrest_agency');
        $form->ignore('temp_arrest_uwa_unit', 'arrest_uwa_unit');
        $form->ignore('temp_arrest_detection_method', 'arrest_detection_method');
        $form->ignore('temp_arrest_uwa_number', 'arrest_uwa_number');
        $form->ignore('temp_arrest_crb_number', 'arrest_crb_number');
        $form->ignore('temp_is_suspect_appear_in_court', 'is_suspect_appear_in_court');
        $form->ignore('temp_prosecutor', 'prosecutor');
        $form->ignore('temp_case_outcome', 'case_outcome');
        $form->ignore('temp_magistrate_name', 'magistrate_name');
        $form->ignore('temp_court_name', 'court_name');
        $form->ignore('temp_court_file_number', 'court_file_number');
        $form->ignore('temp_is_jailed', 'is_jailed');
        $form->ignore('temp_jail_period', 'jail_period');
        $form->ignore('temp_is_fined', 'is_fined');
        $form->ignore('temp_fined_amount', 'fined_amount');
        $form->ignore('temp_status', 'status');



        $form->disableCreatingCheck();
        $form->disableReset();
        //$form->disableEditingCheck();





        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
        });



        if (
            ((!$form->isCreating()) &&
                (Auth::user()->isRole('admin')))
        ) {


            $form->hidden('reported_by', __('Reported by'))->default(Admin::user()->id)->rules('required');

            $form->tab('Case information', function (Form $form) {


                /*        $form->listbox('offences', 'Offences')->options(Offence::all()->pluck('name', 'id'))
                ->help("Select offences involded in this case")
                ->rules('required'); */

                $form->disableCreatingCheck();
                $form->disableReset();
                $form->disableEditingCheck();
                $form->disableViewCheck();




                /*        $form->listbox('offences', 'Offences')->options(Offence::all()->pluck('name', 'id'))
                    ->help("Select offences involded in this case")
                    ->rules('required');
         */

                $form->text('title', __('Case title'))
                    ->help("Describe this case in summary")
                    ->rules('required');
                $form->textarea('offence_description', __('Case description'))
                    ->help("Describe this case in details")
                    ->rules('required');

                $form->text('officer_in_charge', 'Officer in charge')->rules('required');

                $form->select('offense_category', __('Case category'))
                    ->options([
                        'Category 1' => 'Category 1',
                        'Category 2' => 'Category 2',
                        'Category 3' => 'Category 3',
                    ])
                    ->rules('required');

                $form->radio('is_offence_committed_in_pa', __('Did the case take place in a PA?'))
                    ->rules('required')
                    ->options([
                        1 => 'Yes',
                        0 => 'No',
                    ])
                    ->default(null)
                    ->when(0, function (Form $form) {


                        /* 
        
                        $form->select('sub_county_id', __('Sub county'))
                            ->rules('required')
                            ->options(Location::get_sub_counties_array());
        
                        $form->text('parish', __('Parish'))->rules('required');
                        $form->text('village', __('Village'))->rules('required');
                        $form->hidden('offence_category_id', __('Village'))->default(1)->value(1); */
                    })->when(1, function (Form $form) {
                        $form->select('pa_id', __('Select PA'))
                            ->rules('required')
                            ->options(PA::all()->pluck('name_text', 'id'));
                    });


                $form->select('ca_id', __('Nearest conservation area'))
                    ->rules('required')
                    ->options(ConservationArea::all()->pluck('name', 'id'));


                $form->text('latitude', 'Case scene GPS - latitude');
                $form->text('longitude', 'Case scene GPS - longitude');




                $form->hidden('has_exhibits', __('Does this case have exhibits?'))
                    ->default(1);

                $form->select('detection_method', __('Detection method'))
                    ->options([
                        'Ambush patrol based on Intelligence' => 'Ambush patrol based on Intelligence',
                        'Contacted by security agencies' => 'Contacted by security agencies',
                        'House visit based on intelligence' => 'House visit based on intelligence',
                        'Intelligence led patrol' => 'Intelligence led patrol',
                        'Observed during non-duty activities' => 'Observed during non-duty activities',
                        'Routine patrol by rangers' => 'Routine patrol by rangers',
                        'Routine security check' => 'Routine security check',
                        'Investigation' => 'Investigation',
                        'Risk profiling' => 'Risk profiling',
                        'Random selection' => 'Random selection'
                    ])
                    ->rules('required');
            });
            $form->tab('Exhibits', function (Form $form) {

                $form->morphMany('exhibits', 'Click on new to add exhibit', function (Form\NestedForm $form) {

                    $form->select('exhibit_catgory', __('Exhibit category'))
                        ->options([
                            'Wildlife' => 'Wildlife',
                            'Implements' => 'Implements',
                            'Implement & Wildlife' => 'Both Implement & Wildlife',
                        ])
                        ->rules('required');
                    $form->text('wildlife', __('Species'));
                    $form->decimal('quantity', __('Quantity (in KGs)'));
                    $form->text('implement', __('Implements'));
                    $form->decimal('number_of_pieces', __('Number of pieces/equipment'));
                    $form->textarea('description', __('Description'))
                        ->rules('required');
                    /* $form->textarea('wildlife', __('Wildlife'));
                $form->textarea('implements', __('Implements')); */

                    $form->file('photos', __('Exhibit file/photo'));
                    $form->file('attachment', __('Attachments'));
                });
            });
        }


        $form->tab('Case progress comments', function (Form $form) {
            $form->morphMany('comments', 'Click on new to add a case progress comment', function (Form\NestedForm $form) {
                $u = Admin::user();
                $form->hidden('comment_by')->default($u->id);

                $form->text('body', __('Progress comment'))->rules('required');
            });
        });


        /*        $form->tab('Case progress AddSuspectAction', function (Form $form) {
            $form->morphMany('AddSuspectAction', 'Click on new to add a case progress comment', function (Form\NestedForm $form) {
                $u = Admin::user();
                $form->hidden('comment_by')->default($u->id);

                $form->text('body', __('Progress comment'))->rules('required');
            });
        }); */



        return $form;
    }
}
