<?php

namespace Encore\Admin\Controllers;

use App\Models\CaseComment;
use App\Models\CaseModel;
use App\Models\CaseSuspect;
use App\Models\CaseSuspectsComment;
use App\Models\Exhibit;
use App\Models\ConservationArea;
use App\Models\User;
use App\Models\Utils;
use Carbon\Carbon;
use Encore\Admin\Admin;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class Dashboard
{

    public static function suspects()
    {
        $suspects = CaseSuspect::orderBy('created_at', 'DESC')->orderBy('updated_at', 'DESC')->limit(6)->get();

        return view('dashboard.suspects', [
            'suspects' => $suspects
        ]);
    }

    public static function cases()
    {
        $cases = CaseModel::where([])
            ->orderBy('id', 'Desc')->limit(9)->get();

        return view('dashboard.cases', [
            'items' => $cases
        ]);
    }

    public static function comments()
    {
        $comments = CaseComment::where([])
            ->orderBy('id', 'Desc')->limit(9)->get();
        return view('dashboard.comments', [
            'items' => $comments
        ]);
    }

    public static function month_ago()
    {

        $data = [];

        //get for the last year per month
        for ($i = 12; $i >= 0; $i--) {
            $min = new Carbon();
            $max = new Carbon();
            $max->subMonths($i);
            $min->subMonths(($i + 1));

            // Check for specimen exhibits
            $ivory = Exhibit::whereBetween('created_at', [$min, $max])
                ->where('specimen', 'like', '%ivory%')->count();

            $pangolin_scales = Exhibit::whereBetween('created_at', [$min, $max])
                ->where('specimen', 'like', '%pangolin scale%')->count();

            $hippo_teeth = Exhibit::whereBetween('created_at', [$min, $max])
                ->where('specimen', 'like', '%hippo teeth%')->count();

            // $ivory = Exhibit::whereBetween('created_at', [$min, $max])
            //     ->where([
            //         'wildlife_species' => 2
            //     ])
            //     ->count();
            // $pangolin = Exhibit::whereBetween('created_at', [$min, $max])
            //     ->where([
            //         'wildlife_species' => 64
            //     ])
            //     ->count();
            // $rhino = Exhibit::whereBetween('created_at', [$min, $max])
            //     ->where([
            //         'wildlife_species' => 6
            //     ])
            //     ->count();
            // $parrot = Exhibit::whereBetween('created_at', [$min, $max])
            //     ->where([
            //         'wildlife_species' => 6
            //     ])
            //     ->count();
            $data['data'][] = $ivory + $pangolin_scales + $hippo_teeth;
            $data['ivory'][] = $ivory;
            $data['pangolin_scales'][] = $pangolin_scales;
            $data['hippo_teeth'][] = $hippo_teeth;
            $data['labels'][] = Utils::month($max);
        }

        return view('dashboard.graph-month-ago', $data);
    }

    public static function graph_suspects()
    {

        // Loop through CASESUSPECTS and toogle is_convicted randomly



        for ($i = 12; $i >= 0; $i--) {
            $min = new Carbon();
            $max = new Carbon();
            $max->subMonths($i);
            $min->subMonths(($i + 1));
            $created_at = CaseSuspect::whereBetween('created_at', [$min, $max])->count();

            $is_suspects_arrested = CaseSuspect::whereBetween('created_at', [$min, $max])
                ->where([
                    'is_suspects_arrested' => 'Yes'
                ])
                ->orWhere([
                    'is_suspects_arrested' => 1
                ])
                ->count();
            $is_suspect_appear_in_court = CaseSuspect::whereBetween('created_at', [$min, $max])
                ->where([
                    'is_suspect_appear_in_court' => 'Yes'
                ])
                ->orWhere([
                    'is_suspect_appear_in_court' => 1
                ])
                ->count();
            $is_jailed = CaseSuspect::whereBetween('created_at', [$min, $max])
                ->where([
                    'court_status' => 'Concluded'
                ])
                ->count();

            $is_fined = CaseSuspect::whereBetween('created_at', [$min, $max])
                ->where([
                    'is_fined' => 'Yes'
                ])
                ->count();


            $is_convicted = CaseSuspect::whereBetween('created_at', [$min, $max])
                ->where([
                    'case_outcome' => 'Convicted'
                ]) 
                ->count(); 
 
            $data['is_convicted'][] = $is_convicted;
            $data['created_at'][] = $created_at;
            $data['is_suspects_arrested'][] = $is_suspects_arrested;
            $data['is_suspect_appear_in_court'][] = $is_suspect_appear_in_court;
            $data['is_jailed'][] = $is_jailed;
            $data['is_fined'][] = $is_fined;
            $data['labels'][] = Utils::month($max);
        }


        return view('dashboard.graph-suspects', $data);
    }

    public static function graph_top_districts()
    {
        $tot = CaseModel::count();

        $data['labels'] = [];
        $data['count'] = [];

        foreach (ConservationArea::where('name', '!=', 'UWA')->get() as $key => $ca) {
            $label = substr($ca->name, 0, 10);
            if (strlen($ca->name) > 15) {
                $label .= "...";
            }
            $cases_count = 0;
            if ($tot > 0) {
                $cases_count  = CaseModel::where('ca_id', $ca->id)->count();
                $per = (int) (($cases_count / $tot) * 100);
                $label .= " ($per%)";
            }
            $data['count'][] = $cases_count;
            $data['labels'][] = $label;
        }
        return view('dashboard.graph-top-districts', [
            'labels' => $data['labels'],
            'count' => $data['count'],
            'totalCases' => $tot
        ]);
    }

    public static function graph_animals()
    {
        $comments = CaseSuspectsComment::where([])
            ->orderBy('id', 'Desc')->limit(9)->get();

        return view('dashboard.graph-animals', [
            'items' => $comments
        ]);
    }

    public static function help_videos()
    {
        return view('widgets.help-videos');
    }

    public static function all_users()
    {
        $u = Auth::user();
        $all_students = User::count();

        $male_students = User::where([
            'user_type' => 'Student',
            'sex' => 'Male',
        ])->count();
        $female_students = $all_students - $male_students;
        $sub_title = number_format($male_students) . ' Males, ';
        $sub_title .= number_format($female_students) . ' Females.';
        return view('widgets.box-5', [
            'is_dark' => false,
            'title' => 'All system users',
            'sub_title' => $sub_title,
            'number' => number_format($all_students),
            'link' => admin_url('auth/users')
        ]);
    }
    public static function all_teachers()
    {
        $all_students = User::where([
            'user_type' => 'employee',
        ])->count();

        $male_students = User::where([
            'user_type' => 'employee',
            'sex' => 'Male',
        ])->count();


        $female_students = $all_students - $male_students;
        $sub_title = number_format($male_students) . ' Males, ';
        $sub_title .= number_format($female_students) . ' Females.';
        return view('widgets.box-5', [
            'is_dark' => false,
            'title' => 'All admins',
            'sub_title' => $sub_title,
            'number' => number_format($all_students),
            'link' => admin_url('auth/users')
        ]);
    }


    public static function all_students()
    {
        $all_students = User::where([
            'user_type' => 'Student',
        ])->count();

        $male_students = User::where([
            'user_type' => 'Student',
            'sex' => 'Male',
        ])->count();

        $female_students = $all_students - $male_students;

        $sub_title = number_format($male_students) . ' Today, ';
        $sub_title .= number_format($female_students) . ' This week.';
        return view('widgets.box-5', [
            'is_dark' => false,
            'title' => 'Transactions',
            'sub_title' => $sub_title,
            'number' => number_format($all_students),
            'link' => admin_url('auth/users')
        ]);
    }




    public static function income_vs_expenses()
    {
        return view('admin.charts.bar', [
            'is_dark' => true
        ]);
    }

    public static function fees_collected()
    {
        return view('admin.charts.pie', [
            'is_dark' => true
        ]);
    }



    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function title()
    {
        return view('admin::dashboard.title');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function environment()
    {
        $envs = [
            ['name' => 'PHP version',       'value' => 'PHP/' . PHP_VERSION],
            ['name' => 'Laravel version',   'value' => app()->version()],
            ['name' => 'CGI',               'value' => php_sapi_name()],
            ['name' => 'Uname',             'value' => php_uname()],
            ['name' => 'Server',            'value' => Arr::get($_SERVER, 'SERVER_SOFTWARE')],

            ['name' => 'Cache driver',      'value' => config('cache.default')],
            ['name' => 'Session driver',    'value' => config('session.driver')],
            ['name' => 'Queue driver',      'value' => config('queue.default')],

            ['name' => 'Timezone',          'value' => config('app.timezone')],
            ['name' => 'Locale',            'value' => config('app.locale')],
            ['name' => 'Env',               'value' => config('app.env')],
            ['name' => 'URL',               'value' => config('app.url')],
        ];

        return view('admin::dashboard.environment', compact('envs'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function extensions()
    {
        $extensions = [
            'helpers' => [
                'name' => 'laravel-admin-ext/helpers',
                'link' => 'https://github.com/laravel-admin-extensions/helpers',
                'icon' => 'gears',
            ],
            'log-viewer' => [
                'name' => 'laravel-admin-ext/log-viewer',
                'link' => 'https://github.com/laravel-admin-extensions/log-viewer',
                'icon' => 'database',
            ],
            'backup' => [
                'name' => 'laravel-admin-ext/backup',
                'link' => 'https://github.com/laravel-admin-extensions/backup',
                'icon' => 'copy',
            ],
            'config' => [
                'name' => 'laravel-admin-ext/config',
                'link' => 'https://github.com/laravel-admin-extensions/config',
                'icon' => 'toggle-on',
            ],
            'api-tester' => [
                'name' => 'laravel-admin-ext/api-tester',
                'link' => 'https://github.com/laravel-admin-extensions/api-tester',
                'icon' => 'sliders',
            ],
            'media-manager' => [
                'name' => 'laravel-admin-ext/media-manager',
                'link' => 'https://github.com/laravel-admin-extensions/media-manager',
                'icon' => 'file',
            ],
            'scheduling' => [
                'name' => 'laravel-admin-ext/scheduling',
                'link' => 'https://github.com/laravel-admin-extensions/scheduling',
                'icon' => 'clock-o',
            ],
            'reporter' => [
                'name' => 'laravel-admin-ext/reporter',
                'link' => 'https://github.com/laravel-admin-extensions/reporter',
                'icon' => 'bug',
            ],
            'redis-manager' => [
                'name' => 'laravel-admin-ext/redis-manager',
                'link' => 'https://github.com/laravel-admin-extensions/redis-manager',
                'icon' => 'flask',
            ],
        ];

        foreach ($extensions as &$extension) {
            $name = explode('/', $extension['name']);
            $extension['installed'] = array_key_exists(end($name), Admin::$extensions);
        }

        return view('admin::dashboard.extensions', compact('extensions'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function dependencies()
    {
        $json = file_get_contents(base_path('composer.json'));

        $dependencies = json_decode($json, true)['require'];

        return Admin::component('admin::dashboard.dependencies', compact('dependencies'));
    }
}
