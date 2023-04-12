<?php

namespace App\Admin\Actions\CaseModel;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class CaseModelAddComment extends RowAction
{
    public $name = 'Add comment';

    public function handle(Model $model)
    {
        return $this->response()->redirect("/comments/{$model->id}/edit");
    }
}
