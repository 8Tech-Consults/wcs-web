<?php

namespace App\Admin\Actions\CaseModel;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class CaseModelActionEditCase extends RowAction
{
    public $name = 'Edit case';

    public function handle(Model $model)
    {
        return $this->response()->redirect(admin_url("/cases/{$model->id}/edit"));
    }
}
