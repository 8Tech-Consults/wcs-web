<?php

namespace App\Admin\Controllers;

use App\Models\Animal;
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



        $pendingCase = Utils::hasPendingCase(Auth::user());
        if ($pendingCase == null) {
            die("active case not found.");
        } else {
        }
        if ($pendingCase->user_adding_suspect_id == Auth::user()->id) {
            $pendingCase->user_adding_suspect_id = null;
            $pendingCase->save();
            Admin::script('window.location.replace("' . admin_url("cases") . '");');
            return 'Loading...';
        }

        $form = new Form(new Exhibit());

        $form->hidden('case_id', 'Suspect photo')->default($pendingCase->id)->value($pendingCase->id);

        Admin::css(url('/css/new-case.css'));

        $form->disableCreatingCheck();
        $form->disableReset();
        $form->disableEditingCheck();
        $form->disableViewCheck();
        $form->html(view('steps', ['active' => 3, 'case' => $pendingCase]));


        $form->html('<a class="btn btn-danger" href="' . admin_url("new-confirm-case-models/{$pendingCase->id}/edit") . '" >SKIP TO SUBMIT</a>', 'SKIP');

        $form->hidden('case_id', 'Suspect photo')->default($pendingCase->id)->value($pendingCase->id);

        $form->radio('type_wildlife', __('Exibit type Wildlife?'))
            ->options([
                'Yes' => 'Yes',
                'No' => 'No',
            ])
            ->when('Yes', function ($form) {
                $form->divider('Wildlife Exibit(s) Information');


                $options =  Animal::where([])->orderBy('id', 'desc')->get()->pluck('name', 'id');
                $form->select('wildlife_species', 'Select Species')->options($options)
                    ->when(1, function ($form) {
                        $form->text('other_wildlife_species', 'Specify Species')
                            ->rules('required');
                    })
                    ->rules('required');


                $form->select('specimen', 'Specimen')->options(
                    array(
                        "Scales" => "Scales",
                        "Ivory" => "Ivory",
                        "Teeth" => "Teeth",
                        "Live animal" => "Live animal",
                        "Meat" => "Meat",
                        "Skin" => "Skin",
                        "Horns" => "Horns",
                        "Tusks" => "Tusks",
                        "Trophies" => "Trophies",
                    )
                )->rules('required');

                $form->text('wildlife_description', __('Description'));
                $form->decimal('wildlife_pieces', __('Number of pieces'));
                $form->decimal('wildlife_quantity', __('Quantity (in KGs)'));
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

                $form->select('implement_name', 'Select implement')->options(
                    array(
                        "Pangas" => "Pangas",
                        "Knives" => "Knives",
                        "Wheal traps" => "Wheal traps",
                        "Spears" => "Spears",
                        "Wire snares" => "Wire snares",
                        "Metal trap" => "Metal trap",
                        "How" => "How",
                        "Axe" => "Axe",
                        "Spade" => "Spade",
                        "Hooks" => "Hooks",
                        "Fishing nets" => "Fishing nets",
                        "Other" => "Other",
                    )
                )->rules('required');


                $form->decimal('implement_pieces', __('No of pieces'));
                $form->textarea('implement_description', __('Description'));
                $form->multipleFile('implement_attachments', __('Implements exhibit(s) attachments files or photos'));
                $form->divider();
            });

        $form->radio('type_other', __('Other exhibit types?'))
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

        $form->radio('add_another_exhibit', __('Do you want to add another exhibit to this case?'))
            ->options([
                'Yes' => 'Yes',
                'No' => 'No',
            ])->required();


        return $form;
    }
}
