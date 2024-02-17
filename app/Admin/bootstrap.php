<?php
 

use Encore\Admin\Facades\Admin;
use App\Admin\Extensions\Nav\Shortcut;
use App\Admin\Extensions\Nav\Dropdown;
use App\Models\Animal;
use App\Models\CaseModel;
use App\Models\CaseSuspect;
use App\Models\Court;
use App\Models\Exhibit;
use App\Models\Utils;
use Carbon\Carbon;
use Encore\Admin\Form;
use Illuminate\Support\Facades\Auth;


Encore\Admin\Form::forget(['map', 'editor']);

$i = 1;
$u = Auth::user();

/* $cases = CaseModel::where('id','>=',2626)->get();
foreach ($cases as $key => $c) {
    Exhibit::where('case_id',$c->id)->delete();
    CaseSuspect::where('case_id',$c->id)->delete();
    CaseModel::where('id',$c->id)->delete();
    $c->delete();
    echo "Deleted case ".$c->id."<br>";
}

die("STOP"); */


Utils::system_boot($u); //DO NOT REMOVE THIS LINE

Admin::css('/css/jquery-confirm.min.css');
Admin::js('/js/charts.js');

Admin::css(url('/assets/bootstrap.css'));
Admin::css('/assets/styles.css');

Form::init(function (Form $form) {

    $form->disableEditingCheck();

    // $form->disableCreatingCheck();

    $form->disableViewCheck();
    $form->disableReset();

    $form->tools(function (Form\Tools $tools) {
        $tools->disableDelete();
        $tools->disableView();
    });
});
