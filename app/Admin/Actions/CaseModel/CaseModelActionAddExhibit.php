<?php

namespace App\Admin\Actions\CaseModel;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class CaseModelActionAddExhibit extends RowAction
{
    public $name = 'Add Exhibits or Files';

    public function handle(Model $model)
    {
        session(['add_exhibit' => $model->id]);
        return $this->response()->redirect(admin_url("/add-exhibit/create"));
    }
}
