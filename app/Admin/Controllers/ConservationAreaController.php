<?php

namespace App\Admin\Controllers;

use App\Models\ConservationArea;
use App\Models\PA;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ConservationAreaController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Conservation Areas';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {

        $grid = new Grid(new ConservationArea());

        $grid->disableBatchActions();
        $grid->column('id', __('Id'))->hide()->sortable();
        $grid->column('name', __('Name'))->sortable();
        $grid->column('description', __('Details'));

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
        $show = new Show(ConservationArea::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('name', __('Name'));
        $show->field('description', __('Description'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new ConservationArea());

        $form->text('name', __('Name'));
        $form->textarea('description', __('Description'));

        return $form;
    }
}
