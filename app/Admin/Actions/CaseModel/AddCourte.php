<?php

namespace App\Admin\Actions\CaseModel;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class AddCourte extends RowAction
{ 
    public $name = 'Add court info';

    public function handle(Model $model)
    {
        return $this->response()->redirect(admin_url("/court-cases/{$model->id}/edit"));
    }
}
