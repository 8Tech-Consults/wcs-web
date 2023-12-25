<?php

/**
 * Laravel-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */

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

// foreach (CaseSuspect::all() as $key => $suspect) {
//     $suspect->is_convicted = ['Yes','No'][rand(0, 1)];
//     $suspect->save();
// }
// die();
Encore\Admin\Form::forget(['map', 'editor']);

$i = 1;
/* $ans = Animal::all()->toArray();

foreach (Exhibit::all() as $key => $c) {
    shuffle($ans);
    $c->wildlife_species = $ans[2]['id'];
    $c->save();
    continue;
    $c->save();
    $t = Carbon::now();
    $c->created_at = $t->subDays(rand(-10, 360));
    $c->updated_at = $c->created_at;

    $c->photo = 'images/' . rand(1, 20) . '.jpg';
    continue;
    $cou = $c->get_photos();
    if ($cou > 10) {
        if (true) {
            $x = 0;
            $new = [];
            for ($j = 0; $j < 2; $j++) {
                $_n = 'images/ex-' . rand(1, 17) . '.jpg';
                $new[] = $_n;
            }
            $c->wildlife_attachments = $new;
            $c->save();

            $x = 0;
            $new = [];
            for ($j = 0; $j < 2; $j++) {
                $_n = 'images/ex-' . rand(1, 17) . '.jpg';
                $new[] = $_n;
            }
            $c->implement_attachments = $new;
            $c->save();

            $x = 0;
            $new = [];
            for ($j = 0; $j < 2; $j++) {
                $_n = 'images/ex-' . rand(1, 17) . '.jpg';
                $new[] = $_n;
            }
            $c->others_attachments = $new;
            $c->save();

            echo ($c->id . "<br>");
        }
        echo count($c->get_photos()) . ". => " . $c->title . " <br> ";
    }
    $t = Carbon::now();
    $c->created_at = $t->subDays(rand(-10, 360));
    $c->updated_at = $c->created_at;
    //    $c->save();  
    $i++;
}
die();
 */
$u = Auth::user();
if ($u != null) {

    /*     if (isset($_GET['log_me_out'])) {
    } else {
        if (isset($_GET['resend_2f_code'])) {
            $u->send2FCode();
            header('Location: ' . url('2fauth'));
        }
        if (!$u->authenticated) {
            if ($u->code == null) {
                $u->send2FCode();
            }
            header('Location: ' . url('2fauth'));
            die();
        }
    } */
} 
Utils::system_boot($u); //DO NOT REMOVE THIS LINE


/* Admin::navbar(function (\Encore\Admin\Widgets\Navbar $navbar) {

 
    $navbar->left(view('admin.search-bar', [
        'u' => $u
    ]));
    $links = [];

    if ($u != null) {

        if ($u->isRole('super-admin')) {
            $links = [
                'Member' => admin_url('/'),
                'Case' => admin_url('/'),
            ];
        }
        if ($u->isRole('admin')) {
            $links = [
                'Member' => admin_url('/'),
                'Case' => admin_url('/'),
            ];
        }
 

        $navbar->left(Shortcut::make($links, 'fa-plus')->title('ADD NEW'));

        $navbar->left(new Dropdown());
 
        $navbar->right(view('widgets.admin-links', [
            'items' => []
        ]));
    }
});
 */
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
