<?php

namespace App\Admin\Controllers;

use App\Models\ImplementType;
use App\Models\Court; 
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ImplementTypeController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */ 
    protected $title = 'Implement Types'; 

    /**
     * Make a grid builder. 
     * 
     * @return Grid
     */
    protected function grid()
    {

        $grid = new Grid(new ImplementType());
        $grid->disableBatchActions();
        $grid->disableFilter();
        $grid->disableExport();
        $grid->column('id', __('S/n'))->width(50)->sortable();
        $grid->column('name', __('Name'))->sortable();
        $grid->column('details', __('Details'));


        $grid->actions(function ($actions) {
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
        $show = new Show(ImplementType::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
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
        $form = new Form(new ImplementType());
 
        $form->text('name', __('Name'))->required();
        $form->textarea('details', __('Details'));



        return $form;
    }
}
