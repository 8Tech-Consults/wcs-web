<?php

namespace App\Admin\Controllers;

use App\Models\Court;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CourtController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Courts';

    /**
     * Make a grid builder. 
     * 
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Court());
        $grid->disableBatchActions();
        $grid->disableFilter();
        $grid->disableExport();
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
        $show = new Show(Court::findOrFail($id));

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
        $form = new Form(new Court()); 

        $form->text('name', __('Name'))->required();
        $form->textarea('details', __('Details'));

        

        return $form;
    }
}
