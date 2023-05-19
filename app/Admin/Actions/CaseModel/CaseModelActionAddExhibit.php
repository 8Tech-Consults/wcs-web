<?php

namespace App\Admin\Actions\CaseModel;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class CaseModelActionAddExhibit extends RowAction
{
    public $name = 'Add exhibit';

    public function handle(Model $model)
    {
        return $this->response()->redirect(admin_url("/cases?add_exhibit_to_case_id={$model->id}"));
    }
}
