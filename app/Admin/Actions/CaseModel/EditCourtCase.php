<?php

namespace App\Admin\Actions\CaseModel;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class EditCourtCase extends RowAction
{ 
    public $name = 'Update Court Info';

    public function handle(Model $model)
    {
        session()->forget('court_case_action'); //unset the update action
        return $this->response()->redirect(admin_url("/court-cases/{$model->id}/edit"));
    }
}
 