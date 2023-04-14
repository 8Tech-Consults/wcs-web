<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\CaseModel\EditCourtCase;
use App\Admin\Actions\CaseModel\EditExhibit;
use App\Models\Animal;
use App\Models\CaseModel;
use App\Models\Exhibit;
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
            $f->between('created_at', 'Filter by date')->date();
            /*             $f->equal('reported_by', "Filter by reporter")
                ->select(Administrator::all()->pluck('name', 'id')); */

            $ajax_url = url(
                '/api/ajax?'
                    . "&search_by_1=title"
                    . "&search_by_2=id"
                    . "&model=CaseModel"
            );

            $f->equal('case_id', 'Filter by Case')->select(function ($id) {
                $a = CaseModel::find($id);
                if ($a) {
                    return [$a->id => "#" . $a->id . " - " . $a->title];
                }
            })
                ->ajax($ajax_url);
        });





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
        $grid->column('type_wildlife', __('Exibit type Wildlife'));
        $grid->column('wildlife_species', __('Wildlife Species Name'));
        $grid->column('wildlife_quantity', __('Wildlife Quantity (in KGs)'));
        $grid->column('wildlife_pieces', __('Wildlife Number of pieces'));
        $grid->column('wildlife_description', __('Wildlife Description'));
        $grid->column('type_implement', __('Exibit type implement'));
        $grid->column('implement_name', __('Exibit name'));
        $grid->column('implement_pieces', __('Implement pieces'));
        $grid->column('implement_description', __('Implement description'));
        $grid->column('type_other', __('Exibit type Others'));
        $grid->column('others_description', __('Description for others'));

        $grid->actions(function ($actions) {
            if (
                (!Auth::user()->isRole('admin'))
            ) {
            }
            $actions->disableEdit();
            $actions->add(new EditExhibit);

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


                $form->select('specimen', 'Specimen')->options(
                    array(
                        "Scales" => "Scales",
                        "Ivory" => "Ivory",
                        "Teeth" => "Teeth",
                        "Live animal" => "Live animal",
                        "Meat" => "Meat",
                        "Skin" => "Skin",
                        "Horns" => "Horns",
                        "Tusks" => "Tusks",
                        "Trophies" => "Trophies",
                    )
                )->rules('required');

                $form->text('wildlife_description', __('Description'));
                $form->decimal('wildlife_pieces', __('Number of pieces'));
                $form->decimal('wildlife_quantity', __('Quantity (in KGs)'));
                $form->multipleFile('wildlife_attachments', __('Wildlife exhibit(s) attachments files or photos'));
                $form->divider();
            });
        $form->radio('type_implement', __('Exibit type Implement?'))
            ->options([
                'Yes' => 'Yes',
                'No' => 'No',
            ])
            ->when('Yes', function ($form) {
                $form->divider('Implements Exibit(s) Information')->rules('required');

                $form->select('implement_name', 'Select implement')->options(
                    array(
                        "Pangas" => "Pangas",
                        "Knives" => "Knives",
                        "Wheal traps" => "Wheal traps",
                        "Spears" => "Spears",
                        "Wire snares" => "Wire snares",
                        "Metal trap" => "Metal trap",
                        "How" => "How",
                        "Axe" => "Axe",
                        "Spade" => "Spade",
                        "Hooks" => "Hooks",
                        "Fishing nets" => "Fishing nets",
                        "Other" => "Other",
                    )
                )->rules('required');


                $form->decimal('implement_pieces', __('No of pieces'));
                $form->textarea('implement_description', __('Description'));
                $form->multipleFile('implement_attachments', __('Implements exhibit(s) attachments files or photos'));
                $form->divider();
            });

        $form->radio('type_other', __('Other exhibit types?'))
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
