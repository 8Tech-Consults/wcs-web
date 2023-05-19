<?php

namespace App\Admin\Actions\CaseModel;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class EditArrest extends RowAction
{ 
    public $name = 'Edit';

    public function handle(Model $model)
    {
        return $this->response()->redirect(admin_url("/arrests/{$model->id}/edit"));
    }
}
