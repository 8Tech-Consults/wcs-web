<?php

namespace App\Admin\Controllers;

use App\Models\CaseModel;
use App\Models\Exhibit;
use App\Models\Utils;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Auth;

class NewExhibitsCaseModelController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Creating new case - exhibits';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {



        $pendingCase = Utils::hasPendingCase(Auth::user());
        if ($pendingCase != null) {

            $ex = Exhibit::where([
                'case_id' => $pendingCase->id
            ])
                ->orderBy('id', 'Desc')
                ->first();
            if ($ex != null) {
                if ($ex->add_another_exhibit == 'Yes') {
                    Admin::script('window.location.replace("' . admin_url("new-exhibits-case-models/create") . '");');
                    return 'Loading...';
                }
            }

            if ($pendingCase->case_step == 1) {
                Admin::script('window.location.replace("' . admin_url('new-case-suspects/create') . '");');
                return 'Loading...';
            } else if ($pendingCase->case_step == 2) {
                Admin::script('window.location.replace("' . admin_url("new-exhibits-case-models/create") . '");');
                return 'Loading...';
            } else if ($pendingCase->case_step == 3) {
                Admin::script('window.location.replace("' . admin_url("new-confirm-case-models/{$pendingCase->id}/edit") . '");');
                return 'Loading...';
            } else {
            }
            //dd($pendingCase);
        }


        $grid = new Grid(new Exhibit());


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

        $pendingCase = Utils::hasPendingCase(Auth::user());
        if ($pendingCase == null) {
            die("active case not found.");
        } else {
            $form->hidden('case_id', 'Suspect photo')->default($pendingCase->id)->value($pendingCase->id);
        }


        Admin::css(url('/css/new-case.css'));

        $form->disableCreatingCheck();
        $form->disableReset();
        $form->disableEditingCheck();
        $form->disableViewCheck();
        $form->html(view('steps', ['active' => 3, 'case' => $pendingCase]));


        $form->html('<a class="btn btn-danger" href="' . admin_url("new-confirm-case-models/{$pendingCase->id}/edit") . '" >SKIP TO SUBMIT</a>', 'SKIP');

        $form->hidden('case_id', 'Suspect photo')->default($pendingCase->id)->value($pendingCase->id);

        $form->radio('exhibit_catgory', __('Exhibit type'))
            ->options([
                'Wildlife' => 'Wildlife',
                'Implement' => 'Implement',
            ])
            ->when('Wildlife', function ($form) {
                $form->text('wildlife', __('Species'));
                $form->text('description', __('Description'))
                    ->help('Explantion E.g skin, scales, meat, live animal, e.t.c');
                $form->decimal('quantity', __('Quantity (in KGs)'));
                $form->decimal('number_of_pieces', __('Number of pieces'));
            })
            ->when('Implement', function ($form) {
                $form->text('implement', __('Implement description'));
                $form->decimal('number_of_pieces', __('Number of pieces'));
            })
            ->rules('required');


        $form->multipleImage('pics', __('Exhibit file/photo'));
        $form->multipleFile('attachment', __('Attachments'));

        $form->radio('add_another_exhibit', __('Do you want to add another exhibit to this case?'))
            ->options([
                'Yes' => 'Yes',
                'No' => 'No',
            ])->required();


        return $form;
    }
}
