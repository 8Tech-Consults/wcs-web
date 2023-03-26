<?php

namespace App\Admin\Actions\CaseModel;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class CaseModelActionAddSuspect extends RowAction
{
    public $name = 'Add suspect';

    public function handle(Model $model)
    {
        return $this->response()->redirect("/new-case-suspects/create?add_suspect_to_case_id={$model->id}");
    }
}
