<?php

namespace App\Admin\Actions\CaseModel;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class CaseModelActionAddSuspect extends RowAction
{
    public $name = 'Add suspect';

    public function handle(Model $model)
    {
        return $this->response()->redirect("/case-suspects/create?case_id={$model->id}");
    }
}
