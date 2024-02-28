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
/* $all = CaseModel::all();
dd($all->count());
foreach ($all as $key => $c) {
    if ($c->pa_id == 1) {
        continue;
    }
    dd($c);
    break;
    if ($c->case_date == null  || strlen($c->case_date) < 3) {
        $c->case_date = $c->created_at;
        $c->save();
    }
    foreach ($c->suspects as $m) {
        $m->case_date =  $c->case_date;
        $m->save();
    }
}
die("STOP"); */
//set unlimited time
/* ini_set('max_execution_time', 0);
ini_set('memory_limit', '-1');

foreach (CaseModel::all() as $key => $c) {
    if ($c->case_date == null  || strlen($c->case_date) < 3) {
        $c->case_date = $c->created_at;
        $c->save();
    }
    foreach ($c->suspects as $m) {
        $m->case_date =  $c->case_date;
        $m->save();
    }
}
 */

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
