<?php

namespace App\Admin\Actions\CaseModel;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class ViewSuspect  extends RowAction
{
    public $name = 'View';

    public function handle(Model $model)
    {
        return $this->response()->redirect(admin_url("/case-suspects/{$model->id}"));
    }
}
