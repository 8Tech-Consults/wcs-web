<?php

namespace App\Admin\Actions\CaseModel;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class UpdateCourte extends RowAction
{ 
    public $name = 'Update court info';

    public function handle(Model $model)
    {
        session()->forget('court_case_action');
        return $this->response()->redirect(admin_url("/court-cases/{$model->id}/edit"));
    }
}
