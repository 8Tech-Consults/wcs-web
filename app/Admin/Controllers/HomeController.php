<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CaseModel;
use App\Models\CaseSuspect;
use App\Models\CaseSuspectsComment;
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


        if (Utils::hasPendingCase(Auth::user()) != null) {
            return redirect(admin_url('case-suspects/create'));
        }




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
            $c->is_convicted = $arrested[1];
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
            $c->is_convicted = $arrested[1];
            shuffle($arrested);
            $c->is_jailed = $arrested[1];
            shuffle($arrested);
            $c->is_fined = $arrested[1];
            $c->save();
        }

        


created_at	
is_suspects_arrested 
is_suspect_appear_in_court	
is_convicted	
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
            $row->column(2, function (Column $column) {
                $column->append(Dashboard::graph_animals());
            });
        });



        return $content;
        return $content->row("Romina Home");
    }
}
