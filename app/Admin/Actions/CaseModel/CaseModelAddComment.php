<?php

namespace App\Admin\Actions\CaseModel;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class CaseModelAddComment extends RowAction
{
    public $name = 'Add comment';

    public function handle(Model $model)
    {
        session(['add_comment' => $model->id]);
        return $this->response()->redirect(admin_url("/case-comments/create"));
    }
}
