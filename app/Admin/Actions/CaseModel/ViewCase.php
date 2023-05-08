<?php

namespace App\Admin\Actions\CaseModel;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class ViewCase  extends RowAction
{
    public $name = 'Show Case';

    public function handle(Model $model)
    {
        return $this->response()->redirect("/cases/{$model->id}");
    }
}
