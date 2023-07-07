<?php

namespace App\Admin\Actions\CaseModel;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class CourtCaseUpdate extends RowAction
{
    public $name = 'update';

    public function handle(Model $model)
    {
        // $model ...

        return $this->response()->success('Success message.')->refresh();
    }

}