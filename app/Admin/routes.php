<?php 
use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

/*     $router->get('/', function () {
        return "love";
    })->name('home'); */
    $router->get('/', 'HomeController@index')->name('home'); 
    $router->resource('cases', CaseModelController::class);
    $router->resource('locations', LocationController::class); 
    $router->resource('exhibits', ExhibitController::class);
    $router->resource('case-suspects', CaseSuspectController::class);
    $router->resource('all-suspects', AllSuspectController::class);
    $router->resource('arrests', ArrestsController::class); 
    $router->resource('court-cases', CourtsController::class); 
    $router->resource('jailed-suspects', JailedSuspectsController::class); 
    $router->resource('p-as', PaController::class);
    $router->resource('offences', OffenceController::class);
    $router->resource('conservation-areas', ConservationAreaController::class);
});
