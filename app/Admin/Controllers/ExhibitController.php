<?php

namespace App\Admin\Controllers;

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
    protected $title = 'Exhibit';

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
        $grid->disableActions();
        $grid->disableBatchActions();
        $grid->column('id', __('ID'))->sortable();
        $grid->column('created_at', __('Date'))->hide()
            ->display(function ($x) {
                return Utils::my_date_time($x);
            })
            ->sortable();
        $grid->column('case_id', __('Case'))
            ->display(function ($x) {
                return $this->case_model->title;
            })
            ->sortable();
        $grid->column('exhibit_catgory', __('Exhibit category'));
        $grid->column('wildlife', __('Wildlife'));
        $grid->column('implements', __('Implements'));
/*         $grid->column('photos', __('Photos')); */
/*         $grid->column('description', __('Description')); */
        $grid->column('quantity', __('Quantity'));

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

        $form->number('case_id', __('Case id'));
        $form->text('exhibit_catgory', __('Exhibit category'));
        $form->textarea('wildlife', __('Wildlife'));
        $form->textarea('implements', __('Implements'));
        $form->textarea('photos', __('Photos'));
        $form->textarea('description', __('Description'));
        $form->number('quantity', __('Quantity'));

        return $form;
    }
}
