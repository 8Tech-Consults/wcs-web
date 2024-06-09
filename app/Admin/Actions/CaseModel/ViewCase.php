<?php

namespace App\Admin\Actions\CaseModel;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class ViewCase  extends RowAction
{
    public $name = 'View Case';

    public function handle(Model $model)
    {
        return $this->response()->redirect(admin_url("/cases/{$model->id}"));
    }
}
