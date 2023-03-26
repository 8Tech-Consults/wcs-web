<?php

namespace App\Admin\Actions\CaseModel;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class AddArrest extends RowAction
{ 
    public $name = 'Add arrest info';

    public function handle(Model $model)
    {
        return $this->response()->redirect("/arrests/{$model->id}/edit");
    }
}
