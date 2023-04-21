<?php

namespace App\Admin\Actions\CaseModel;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class ViewSuspect  extends RowAction
{
    public $name = 'Show';

    public function handle(Model $model)
    {
        return $this->response()->redirect("/case-suspects/{$model->id}");
    }
}
