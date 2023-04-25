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

    } 
    /*  

        $faker = Faker::create();
        $ids = []; 
        foreach (CaseSuspect::all() as $key => $s){
            $ids[] = $s->id;
        }
        foreach (SuspectHasOffence::all() as $key => $s){
 
            shuffle($ids);
            $s->case_suspect_id = $ids[rand(10,20)]; 
            $s->save(); 
      

  
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
            $v->save()
        }
        dd("done"); */

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
