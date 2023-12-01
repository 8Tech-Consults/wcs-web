<?php

namespace App\Admin\Controllers;

use App\Models\SuspectLink;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class SuspectLinkController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'SuspectLink';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new SuspectLink());

        $grid->column('id', __('Id'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        $grid->column('suspect_id_1', __('Suspect id 1'));
        $grid->column('suspect_id_2', __('Suspect id 2'));
        $grid->column('case_id_1', __('Case id 1'));
        $grid->column('case_id_2', __('Case id 2'));

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
        $show = new Show(SuspectLink::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('suspect_id_1', __('Suspect id 1'));
        $show->field('suspect_id_2', __('Suspect id 2'));
        $show->field('case_id_1', __('Case id 1'));
        $show->field('case_id_2', __('Case id 2'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new SuspectLink());

        $form->number('suspect_id_1', __('Suspect id 1'));
        $form->number('suspect_id_2', __('Suspect id 2'));
        $form->number('case_id_1', __('Case id 1'));
        $form->number('case_id_2', __('Case id 2'));

        return $form;
    }
}
