<?php

namespace App\Admin\Actions\CaseModel;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class EditExhibit extends RowAction
{
    public $name = 'Edit';

    public function handle(Model $model)
    {
        return $this->response()->redirect("/exhibits/{$model->id}/edit");
    }
}
