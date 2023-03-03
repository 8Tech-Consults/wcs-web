<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CaseModel;
use App\Models\CaseSuspect;
use App\Models\CaseSuspectsComment;
use App\Models\ConservationArea;
use App\Models\Exhibit;
use App\Models\MenuItem;
use App\Models\PA;
use App\Models\Utils;
use Carbon\Carbon;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;
use Illuminate\Support\Facades\Auth;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(Content $content)
    {
 
        

       /*  $faker = Faker::create();
        foreach (CaseSuspect::all() as $c) {
            $c->created_at = $faker->dateTimeBetween('-12 month', '+2 month');
            $c->save();
        } */


        /*         foreach (Exhibit::all() as $c) {
            $c->photos = 'ex-' . rand(1, 17) . '.jpg';
            $c->description = 'Some description about this exhibit....';
            $c->implement =   $c->implements;
            $c->save();
        }
 */



        /*



        if (Utils::hasPendingCase(Auth::user()) != null) {
            return redirect(admin_url('case-suspects/create'));
        }


        $faker = Faker::create();

        $cas = [];
        foreach (ConservationArea::all() as $key => $c) {
            $cas[] = $c->id;
        }
        $pas = [];
        foreach (PA::all() as $key => $c) {
            $pas[] = $c->id;
        }
        $dms  = [
            'Ambush patrol based on Intelligence' => 'Ambush patrol based on Intelligence',
            'Contacted by security agencies' => 'Contacted by security agencies',
            'House visit based on intelligence' => 'House visit based on intelligence',
            'Intelligence led patrol' => 'Intelligence led patrol',
            'Observed during non-duty activities' => 'Observed during non-duty activities',
            'Routine patrol by rangers' => 'Routine patrol by rangers',
            'Routine security check' => 'Routine security check',
            'Investigation' => 'Investigation',
            'Risk profiling' => 'Risk profiling',
            'Random selection' => 'Random selection'
        ];

        $outs = [
            'Charged' => 'Charged',
            'Remand' => 'Remand',
            'Bail' => 'Bail',
            'Perusal' => 'Perusal',
            'Further investigation' => 'Further investigation',
            'Dismissed' => 'Dismissed',
            'Convicted' => 'Convicted',
            'UWA' => 'UWA',
        ];
        $mas = [
            'Fined' => 'Fined',
            'Cautioned' => 'Cautioned',
            'Police bond' => 'Police bond',
            'Skipped bond' => 'Skipped bond'
        ];

        $cat = [
            'Wildlife' => 'Wildlife',
            'Implements' => 'Implements',
            'Implement & Wildlife' => 'Both Implement & Wildlife',
        ];
        $spe = [
            'Lion',
            'Timber',
            'Giraffe',
            'Chimpanzee',
            'Leopard',
            'Black Rhino',
            'Gorilla',
        ];
        $implements = [
            'Panga',
            'Knife',
            'Gun',
            'Poison',
            'Trap',
        ];

        Exhibit::where([])->delete();
        for ($x = 0;$x < 150;$x++) {
            $c =  new Exhibit();
            $c->case_id = rand(1,50);
            $c->photos = 'ex-'.rand(1,17).'jpg';
            $c->description = 'Some description about this exhibit....';

            shuffle( $cat);
            shuffle( $spe);
            shuffle( $implements);
            $c->exhibit_catgory =  $cat[1];
            $c->wildlife =  $spe[1];
            $c->implements =  $implements[1];
            $c->implement =  $implements[1];
            $c->quantity = rand(4,20);
            $c->save();
        }

        dd("done");









Edit Ed


        */


        /*
        $faker = Faker::create();
        foreach (CaseSuspect::all() as $c) {
            $c->created_at = $faker->dateTimeBetween('-13 month', '+1 month');
            $c->save();
        }

        $pas = [];
        $vars = [1, 2,
        11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,
        3, 4, 5, 6, 8, 9, 10, 11, 12, 13, 13, 13, 1, 1, 1];
        foreach (PA::all() as $key => $pa) {
            shuffle($vars);
            shuffle($vars);
            shuffle($vars);
            $pas[] = $pa->id;
            if ($vars[3] % 2 == 1) {
                $pas[] = $pa->id;
            }
        }

        foreach (CaseModel::all() as $key => $case) {
            shuffle($vars);
            shuffle($vars);
            shuffle($vars);
            shuffle($pas);
            shuffle($pas);
            shuffle($pas);
            if ($vars[3] % 2 == 1) {
                $case->is_offence_committed_in_pa = true;
                $case->pa_id = $pas[4];
            }else{
                $case->is_offence_committed_in_pa = false;
                $case->pa_id = null;
            }
            $case->save();
        }

        die("domina");*/



        /*
$arrested = [0, 1];
        foreach (CaseSuspect::all() as $c) {
            shuffle($arrested);
            $c->is_suspects_arrested = $arrested[1];
            shuffle($arrested);
            $c->is_suspect_appear_in_court = $arrested[1];
            shuffle($arrested);
            shuffle($arrested);
            $c->is_jailed = $arrested[1];
            shuffle($arrested);
            $c->is_fined = $arrested[1];
            $c->save();
        }


        $arrested = [0, 1];
        foreach (CaseSuspect::all() as $c) {
            shuffle($arrested);
            $c->is_suspects_arrested = $arrested[1];
            shuffle($arrested);
            $c->is_suspect_appear_in_court = $arrested[1];
            shuffle($arrested);
            shuffle($arrested);
            $c->is_jailed = $arrested[1];
            shuffle($arrested);
            $c->is_fined = $arrested[1];
            $c->save();
        }




created_at
is_suspects_arrested
is_suspect_appear_in_court
is_jailed
is_fined



                $arrested = [0, 1];
        foreach (CaseSuspect::all() as $c) {
            //shuffle($arrested);
            $c->is_suspects_arrested = $arrested[1];
            $c->save();
        }

        $faker = Faker::create();
        foreach (CaseSuspect::all() as $c) {
            $c->created_at = $faker->dateTimeBetween('-1 year');
            $c->court_date = $c->created_at;
            $c->created_at = $c->created_at;
            $c->arrest_date_time = $c->created_at;
            echo $c->arrest_date_time . "<hr>";
            $c->save();
        }

        $admins = [];
        foreach (Administrator::all() as $a) {
            $admins[] = $a->id;
        }
        foreach (CaseSuspect::all() as $v) {
            $cases = [
                'Found 3 ivory pieces.',
                "Found a lion\'s with {$v->name} ",
                "{$v->name} set to appear to court",
                "{$v->name} approved guilty for the mentioned case. Charged with 3 months in jail.",
                "{$v->name} was released from jail.",
                "{$v->name} was found innocent.",
                "{$v->name} was found with 3 live wild birds",
                "{$v->name} was arrested by XYZ police.",
                "{$v->name} was arrested by XYZ police.",
            ];
            shuffle($cases);
            shuffle($admins);

            $x = new CaseSuspectsComment();
            $x->suspect_id = $v->id;
            $x->comment_by = $admins[1];
            $x->body = $cases[2];
            $x->save();
        }
        dd(count(CaseSuspectsComment::all())); */

        $content
            ->title('Online Wildlife Offenders Database - Dashboard')
            ->description('Hello ' . Auth::user()->name . "!");

        $content->row(function (Row $row) {
            $row->column(4, function (Column $column) {
                $column->append(Dashboard::month_ago());
            });
            $row->column(4, function (Column $column) {
                $column->append(Dashboard::graph_suspects());
            });
            $row->column(4, function (Column $column) {
                $column->append(Dashboard::graph_top_districts());
            });
        });


        $content->row(function (Row $row) {
            $row->column(3, function (Column $column) {
                $column->append(Dashboard::cases());
            });

            $row->column(6, function (Column $column) {
                $column->append(Dashboard::suspects());
            });

            $row->column(3, function (Column $column) {
                $column->append(Dashboard::comments());
            });
            /* $row->column(2, function (Column $column) {
                $column->append(Dashboard::graph_animals());
            }); */
        });



        return $content;
        return $content->row("Romina Home");
    }
}
