<?php

namespace App\Admin\Actions\CaseModel;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class CourtCaseUpdate extends RowAction
{
    public $name = 'Update';

    public function handle(Model $model)
    {
        // $model ...
        session(['court_case_action' => 'update'] );
        return $this->response()->redirect(admin_url("/court-cases/{$model->id}/edit"));
    }

}