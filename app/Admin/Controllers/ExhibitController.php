<?php

namespace App\Admin\Controllers;

use App\Models\Exhibit;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

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

        $grid->column('id', __('Id'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        $grid->column('case_id', __('Case id'));
        $grid->column('exhibit_catgory', __('Exhibit catgory'));
        $grid->column('wildlife', __('Wildlife'));
        $grid->column('implements', __('Implements'));
        $grid->column('photos', __('Photos'));
        $grid->column('description', __('Description'));
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
        $show->field('exhibit_catgory', __('Exhibit catgory'));
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
        $form->text('exhibit_catgory', __('Exhibit catgory'));
        $form->textarea('wildlife', __('Wildlife'));
        $form->textarea('implements', __('Implements'));
        $form->textarea('photos', __('Photos'));
        $form->textarea('description', __('Description'));
        $form->number('quantity', __('Quantity'));

        return $form;
    }
}
