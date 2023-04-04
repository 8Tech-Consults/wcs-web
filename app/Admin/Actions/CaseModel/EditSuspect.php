<?php

namespace App\Admin\Actions\CaseModel;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class EditSuspect extends RowAction
{ 
    public $name = 'Edit';

    public function handle(Model $model)
    {
        return $this->response()->redirect("/case-suspects/{$model->id}/edit");
    }
}
