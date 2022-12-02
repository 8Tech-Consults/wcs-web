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
use App\Models\Utils;
use Illuminate\Support\Facades\Auth;

Encore\Admin\Form::forget(['map', 'editor']);


$u = Auth::user();
Utils::system_boot($u);


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
