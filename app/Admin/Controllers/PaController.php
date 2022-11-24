<?php

namespace App\Admin\Controllers;

use App\Models\Location;
use App\Models\PA;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class PAController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'PA';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new PA());

        $grid->column('id', __('pa #ID'));
        $grid->column('subcounty', __('Subcounty'));
        $grid->column('name', __('Name'));
        $grid->column('details', __('Details'));

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
        $show = new Show(PA::findOrFail($id));

        $show->field('id', __('PA #ID'));
        $show->field('subcounty', __('Subcounty'));
        $show->field('name', __('Name'));
        $show->field('details', __('Details'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new PA());

        $form->select('subcounty', __('Sub county'))
            ->rules('int|required')
            ->options(Location::get_sub_counties()->pluck('name_text', 'id'));
        $form->text('name', __('Name'))->rules('required');
        $form->textarea('details', __('Details'));

        return $form;
    }
}
