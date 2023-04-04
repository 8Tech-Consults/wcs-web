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
    protected $title = 'Adding new exhibit to case';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {

        Admin::script('window.location.replace("' . admin_url("cases") . '");');
        return 'Loading...';
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {

        Admin::script('window.location.replace("' . admin_url("cases") . '");');
        return 'Loading...';

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
        $form->disableEditingCheck();

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


                $form->select('wildlife_species', 'Select Species')->options(
                    array(
                        "Pangolin scales" => "Pangolin scales",
                        "Ivory" => "Ivory",
                        "Hippo teeth" => "Hippo teeth",
                        "Live pangolins" => "Live pangolins",
                        "Bush meat" => "Bush meat",
                        "Skins" => "Skins",
                        "Rhino horns" => "Rhino horns",
                        "Elephant tusks" => "Elephant tusks",
                        "Dead wild animal" => "Dead wild animal",
                        "Live wild animal" => "Live wild animal",
                        "Dead wild bird" => "Dead wild bird",
                        "Live wild bird" => "Live wild bird",
                        "Wildlife trophies" => "Wildlife trophies",
                        "Animal parts" => "Animal parts",
                        "Horns" => "Horns",
                        "Scales" => "Scales"
                    )
                )->rules('required');

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
                        "Fishing nets" => "Fishing nets"
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
