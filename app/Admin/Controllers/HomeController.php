<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\CaseModel;
use App\Models\CaseSuspect;
use App\Models\CaseSuspectsComment;
use App\Models\ConservationArea;
use App\Models\Exhibit;
use App\Models\MenuItem;
use App\Models\PA;
use App\Models\SuspectHasOffence;
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
        return $content;

 
  /* 
        $faker = Faker::create();
        $ids = []; 
        foreach (CaseModel::all() as $key => $s){
            $ids[] = $s->id;
        }


        $Animals =  Animal::where([])->orderBy('id', 'desc')->get(); 
        foreach (Exhibit::all() as $key => $s){
            shuffle($ids);
         
            $s->case_id = $ids[rand(10,20)]; 
            $x = rand(1,17);
            $pics[] = 'images/ex-'.$x.'.jpg'; 
            $x = rand(1,17);
            $pics[] = 'images/ex-'.$x.'.jpg'; 
            $x = rand(1,17);
            $pics[] = 'images/ex-'.$x.'.jpg'; 
            $x = rand(1,17);
            $pics[] = 'images/ex-'.$x.'.jpg'; 
            $x = rand(1,17);
            $pics[] = 'images/ex-'.$x.'.jpg'; 
            $x = rand(1,17);
            $pics[] = 'images/ex-'.$x.'.jpg'; 
            $x = rand(1,17);
            $pics[] = 'images/ex-'.$x.'.jpg'; 
            
            $x = rand(1,17);
            $pics2[] = 'images/ex-'.$x.'.jpg'; 
            $x = rand(1,17);
            $pics2[] = 'images/ex-'.$x.'.jpg'; 
            $x = rand(1,17);
            $pics2[] = 'images/ex-'.$x.'.jpg'; 
            $x = rand(1,17);
            $pics2[] = 'images/ex-'.$x.'.jpg'; 
            $x = rand(1,17);
            $pics2[] = 'images/ex-'.$x.'.jpg'; 
            $x = rand(1,17);
            $pics2[] = 'images/ex-'.$x.'.jpg'; 
            $x = rand(1,17);
            $pics2[] = 'images/ex-'.$x.'.jpg'; 
            $x = rand(1,17);

            shuffle($pics2);
            shuffle($pics);

            $s->wildlife_attachments = $pics; 
            $s->implement_attachments = $pics2; 
            $s->quantity = rand(1,25); 
            $s->wildlife_quantity = rand(1,25); 
            $s->number_of_pieces = rand(1,25); 
            $s->wildlife_pieces = rand(1,10); 
            $s->reported_by = 1; 
            $s->type_wildlife = ['Yes','No'][rand(0,1)]; 
            $s->type_implement = ['Yes','No'][rand(0,1)]; 
            $s->type_other = ['Yes','No'][rand(0,1)]; 
            $s->wildlife_desciprion = 'Some details about this exhibit....'; 
            $s->wildlife_description = 'Some details about this wildlife exhibit....'; 
            $s->wildlife_species = $Animals[rand(0,($Animals->count()-1))]->id; 
 
            $s->save(); 
        }

  
        die("romina"); 
 
 


        foreach (CaseSuspect::where([])->orderBy('id', 'desc')->get() as $key => $v) {

            if (
                $v->is_suspect_appear_in_court == 'Yes' ||
                $v->is_suspect_appear_in_court == 'No'
            ) {
                continue;
            }
            if (
                $v->is_suspect_appear_in_court == '1' ||
                $v->is_suspect_appear_in_court == 1
            ) {
                $v->is_suspect_appear_in_court = 'Yes';
            } else {
                $v->is_suspect_appear_in_court = 'No';
            }
            $v->save();
        }
        dd("done");  */

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
