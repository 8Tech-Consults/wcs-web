<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\CaseModel\EditCourtCase;
use App\Admin\Actions\CaseModel\EditExhibit;
use App\Models\Animal;
use App\Models\CaseModel;
use App\Models\ConservationArea;
use App\Models\Exhibit;
use App\Models\ImplementType;
use App\Models\Specimen;
use App\Models\Utils;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Auth;

class ExhibitController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Exhibits';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Exhibit());



        $grid->filter(function ($f) {
            // Remove the default id filter
            $f->disableIdFilter();
            $f->between('created_at', 'Filter by case date')->date();
            /*             $f->equal('reported_by', "Filter by complainant")
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

            $f->equal('wildlife_species', "Filter by Wildlife Species")
                ->select(Animal::all()->pluck('name', 'id'));

            $f->equal('specimen', "Filter by specimen")
                ->select(Specimen::pluck('name', 'name'));

            $f->equal('implement_name', "Filter by Implement type")
                ->select(ImplementType::where([])->orderBy('id', 'desc')->get()->pluck('name', 'id'));


            $f->equal('created_by_ca_id', "Filter by CA of Entry")
                ->select(ConservationArea::all()->pluck('name', 'id'));
        });


        $grid->model()
            ->orderBy('id', 'Desc');

        $grid->disableCreateButton();


        $grid->disableBatchActions();
        $grid->column('id', __('ID'))->sortable();
        $grid->column('created_at', __('Date'))->hide()
            ->display(function ($x) {
                return Utils::my_date_time($x);
            })
            ->sortable();
        $grid->column('case_id', __('Case'))
            ->display(function ($x) {
                if ($this->case_model == null) {
                    $this->case_model->delete();
                }
                return $this->case_model->case_number;
            })
            ->sortable();
        $grid->column('type_wildlife', __('Has Wildlife'));
        $grid->column('wildlife_species', __('Wildlife Species'))->display(function () {
            return $this->get_species();
        });
        $grid->column('specimen', __('Specimen'));
        $grid->column('wildlife_quantity', __('Wildlife Quantity (in KGs)'));
        $grid->column('wildlife_pieces', __('Wildlife Number of pieces'));
        $grid->column('wildlife_description', __('Wildlife Description'));
        $grid->column('type_implement', __('Has implement'));
        $grid->column('implement_name', __('Implement'))->display(function () {
            return $this->get_implement();
        });
        $grid->column('implement_pieces', __('Implement pieces'));
        $grid->column('implement_description', __('Implement description'));
        $grid->column('type_other', __('Has Others'));
        $grid->column('others_description', __('Description for others'));


        $user = Auth::user();

        $grid->actions(function ($actions) {
            $user = Auth::user();
            if (!$user->isRole('admin')) {
                $actions->disableDelete();
            }

            $actions->disableEdit();

            if ($user->isRole('admin') || $user->isRole('ca-manager')) {
                if ($user->isRole('ca-manager')) {
                    if ($user->ca_id == $actions->row->created_by_ca_id) {
                        $actions->add(new EditExhibit);
                    }
                } else {
                    $actions->add(new EditExhibit);
                }
            }
        });

        $grid->column('created_by_ca_id', __('CA of Entry'))
            ->display(function () {
                if ($this->case->created_by_ca == null) {
                    return  "-";
                }
                return $this->case->created_by_ca->name;
            })
            ->sortable();

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
        $c = Exhibit::findOrFail($id);
        return view('admin.exhibit-details', [
            'e' => $c
        ]);

        $show = new Show(Exhibit::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('case_id', __('Case id'));
        $show->field('exhibit_catgory', __('Exhibit category'));
        $show->field('wildlife', __('Wildlife'));
        $show->field('implements', __('Implements'));
        $show->field('photos', __('Photos'));
        $show->field('description', __('Description'));
        $show->field('quantity', __('Quantity'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {


        $form = new Form(new Exhibit());

        $form->disableCreatingCheck();
        $form->disableReset();
        $form->disableEditingCheck();
        $form->disableViewCheck();

        $form->radio('type_wildlife', __('Exibit type Wildlife?'))
            ->options([
                'Yes' => 'Yes',
                'No' => 'No',
            ])
            ->when('Yes', function ($form) {
                $form->divider('Wildlife Exibit(s) Information');


                $options =  Animal::where([])->orderBy('id', 'desc')->get()->pluck('name', 'id');
                $form->select('wildlife_species', 'Select Species')->options($options)
                    ->when(1, function ($form) {
                        $form->text('other_wildlife_species', 'Specify Species')
                            ->rules('required');
                    })
                    ->rules('required');


                $form->select('specimen', 'Specimen')->options(Specimen::pluck('name', 'name'))->rules('required');

                $form->decimal('wildlife_quantity', __('Quantity (in KGs)'));
                $form->decimal('wildlife_pieces', __('Number of pieces'));
                $form->text('wildlife_description', __('Description'))->rules('required');
                $form->multipleFile('wildlife_attachments', __('Wildlife exhibit(s) attachments files or photos'))
                    ->downloadable()
                    ->removable();


                $form->divider();
            });
        $form->radio('type_implement', __('Exibit type Implement?'))
            ->options([
                'Yes' => 'Yes',
                'No' => 'No',
            ])
            ->when('Yes', function ($form) {
                $form->divider('Implements Exibit(s) Information')->rules('required');

                $options =  ImplementType::where([])->orderBy('id', 'desc')->get()->pluck('name', 'id');
                $form->select('implement_name', 'Select implement')->options($options)
                    ->when(1, function ($form) {
                        $form->text('other_implement', 'Specify implement')
                            ->rules('required');
                    })
                    ->rules('required');

                $form->decimal('implement_pieces', __('No of pieces'));
                $form->text('implement_description', __('Description'));
                $form->multipleFile('implement_attachments', __('Implements exhibit(s) attachments files or photos'));
                $form->divider();
            });

        $form->radio('type_other', __('Other Exhibits or Files'))
            ->options([
                'Yes' => 'Yes',
                'No' => 'No',
            ])
            ->when('Yes', function ($form) {
                $form->divider('Other Exibit(s) Information')->rules('required');
                $form->text('others_description', __('Description for others'));
                $form->multipleFile('others_attachments', __('Attachments'));
                $form->divider();
            });

        return $form;
    }
}
