<?php

namespace App\Admin\Controllers;

use App\Models\CaseComment;
use App\Models\CaseModel;
use App\Models\User;
use App\Models\Utils;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CaseCommentController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Case Progress Comments';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CaseComment());
        $grid->filter(function ($f) {
            $f->disableIdFilter();
            $f->between('created_at', 'Filter by date of entry')->date();

            $ajax_url = url(
                '/api/ajax?'
                    . "&search_by_1=title"
                    . "&search_by_2=id"
                    . "&model=CaseModel"
            );

            $f->equal('case_id', 'Filter by case')->select(function ($id) {
                $a = CaseModel::find($id);
                if ($a) {
                    return [$a->id => "#" . $a->id . " - " . $a->title];
                }
            })
                ->ajax($ajax_url);
        });
        $grid->model()->orderBy('created_at', 'desc');
        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->disableBatchActions();
        $grid->quickSearch('body')->placeholder('Search comment');
        $grid->column('created_at', __('Date'))->display(function ($created_at) {
            return Utils::my_date_time($created_at);
        })->sortable()
            ->width(120);

        $grid->column('body', __('Body'))->sortable();
        $grid->column('case_id', __('Case'))->display(function ($case_id) {
            $case = CaseModel::find($case_id);
            if ($case != null) {
                $url = admin_url('cases/' . $case->id);
                $txt = $case->case_number . ' - ' . $case->title;
                return "<a target='_blank' href='$url'>$txt</a>";
            } else {
                return 'Case not found';
            }
        })->sortable();
        $grid->column('comment_by', __('Comment by'))->display(function ($comment_by) {
            $reporter = User::find($comment_by);
            if ($reporter != null) {
                return $reporter->name;
            } else {
                return 'Reporter not found';
            }
        })->sortable();


        $user = Admin::user();
        if (!$user->isRole('admin')) {
            $grid->disableActions();
        }
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
        $show = new Show(CaseComment::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('case_id', __('Case id'));
        $show->field('comment_by', __('Comment by'));
        $show->field('body', __('Body'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new CaseComment());

        $add_comment = session('add_comment');
        $case = null;
        $case = CaseModel::find($add_comment);
        if ($case != null) {
        } else {
            //get segment id
            $add_comment = request()->segment(2);
            $case = CaseModel::find($add_comment);
        }
        if ($case == null) {
            //redirect to back
            throw new \Exception("Case not found");
        }
        $comments_html = '<ul>';
        foreach ($case->comments as $comment) {
            //make it a format, date, comment - by
            $comments_html .= '<li><b>' . Utils::my_date_time($comment->created_at) . ':</b> ' . $comment->body . ' - By ' . $comment->reporter->name . '</li>';
        }
        $comments_html .= '</ul>';

        $form->display('Case')->default($case->case_number);

        //check if comments are empty
        if ($case->comments->isEmpty()) {
            $comments_html = 'No comments yet';
        }
        $form->html($comments_html, 'Previous comments');
        $form->divider();
        $form->disableCreatingCheck();

        $form->hidden('case_id', __('Case id'))->default($add_comment);
        $u = Admin::user();
        $form->hidden('comment_by', __('Comment by'))->default($u->id);
        //display commenter by name
        $form->display('Comment by')->default($u->name);
        $form->disableEditingCheck();
        $form->disableCreatingCheck();
        $form->textarea('body', __('Body'))->rules('required')->help('Add a comment to the case progress')->required();

        //on successful submit, reset the session and redirect to the case comments page
        $form->saved(function (Form $form) {
            session(['add_comment' => null]);
            return redirect(admin_url('case-comments'));
        });

        return $form;
    }
}
