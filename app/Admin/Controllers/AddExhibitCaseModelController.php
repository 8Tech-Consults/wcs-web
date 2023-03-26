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

class AddExhibitCaseModelController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Addming new exhibit to case';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {

        Admin::script('window.location.replace("' . admin_url("cases") . '");');
        return 'Loading...';


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


        $pendingCase = null;
        if (isset($_GET['add_exhibit_to_case_id'])) {
            $pendingCase = CaseModel::find($_GET['add_exhibit_to_case_id']);
            if ($pendingCase != null) {
                $x = new Exhibit();
                $x->case_id =  $pendingCase->id;
                $x->reported_by =  Auth::user()->id;
                $x->save();
                Admin::script('window.location.replace("' . admin_url("add-exhibit/{$x->id}/edit") . '");');
                return 'Loading...';
            }
        }

        $arr = (explode('/', $_SERVER['REQUEST_URI']));
        $pendingCase = null;
        $ex = Exhibit::find($arr[2]);
        if ($ex != null) {
            $pendingCase = CaseModel::find($ex->case_id);
        } else {
            die("Exhibit not found.");
        }
        if ($pendingCase == null) {
            die("Case not found.");
        }


        $form = new Form(new Exhibit());

        $form->hidden('case_id', 'Suspect photo')->default($pendingCase->id)->value($pendingCase->id);
        $form->display('ADDING EXHIBIT TO CASE')->default($pendingCase->case_number);
        $form->divider();

        $form->disableCreatingCheck();
        $form->disableReset();
        $form->disableViewCheck();

        $form->radio('type_wildlife', __('Exibit type Wildlife?'))
            ->options([
                'Yes' => 'Yes',
                'No' => 'No',
            ])
            ->when('Yes', function ($form) {
                $form->divider('Wildlife Exibit(s) Information');
                $form->text('wildlife_species', __('Species Name'))->rules('required')
                    ->help('Explantion E.g skin, scales, meat, live animal, e.t.c');
                $form->decimal('wildlife_quantity', __('Quantity (in KGs)'));
                $form->decimal('wildlife_pieces', __('Number of pieces'));
                $form->text('wildlife_description', __('Description'));
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
                $form->text('implement_name', __('Name of implement'));
                $form->decimal('implement_pieces', __('No of pieces'));
                $form->textarea('implement_description', __('Description'));
                $form->multipleFile('implement_attachments', __('Implements exhibit(s) attachments files or photos'));
                $form->divider();
            });

        $form->radio('type_other', __('Exibit type Others?'))
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


        /*  $form->hidden('add_another_exhibit', __('Attachments'))->value('No')->default('No');
 */
        $form->radio('add_another_exhibit', __('Do you want to add another exhibit to this case?'))
            ->options([
                'Yes' => 'Yes',
                'No' => 'No',
            ])->required();


        return $form;
    }
}
