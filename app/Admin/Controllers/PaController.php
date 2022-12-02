<?php

namespace App\Admin\Controllers;

use App\Models\Location;
use App\Models\PA;
use App\Models\Utils;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class PaController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Protected areas';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new PA());

        $grid->disableBatchActions();
        $grid->column('id', __('Id'))->sortable();
        $grid->column('name', __('Protected area'));

        $grid->column('Cases', __('Cases'))
            ->display(function ($x) {
                return count($this->cases);
            })
            ->sortable();

        $grid->column('subcounty', __('Sub-county'))
            ->display(function ($x) {
                return Utils::get(Location::class, $this->subcounty)->name_text;
            })
            ->sortable();


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

        $show->field('id', __('Id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
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

        $form->text('name', __('Protected Area Name'))->rules('required');
        $form->text('short_name', __('Short name'))->rules('required');

        $form->select('subcounty', __('Sub county'))
            ->rules('int|required')
            ->help('Where this PA is located')
            ->options(Location::get_sub_counties()->pluck('name_text', 'id'));

        $form->textarea('details', __('Details'));

        return $form;
    }
}
