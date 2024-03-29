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
    $router->resource('secretaries', SecretariesController::class);

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
    $router->resource('courts', CourtController::class);
    $router->resource('animals', AnimalController::class);
    $router->resource('implements', ImplementTypeController::class);
    $router->resource('specimens', SpecimenController::class);

    $router->resource('new-case', NewCaseModelController::class);
    $router->resource('new-case-suspects', NewCaseSuspectController::class);
    $router->resource('new-exhibits-case-models', NewExhibitsCaseModelController::class);
    $router->resource('add-exhibit', AddExhibitCaseModelController::class);
    $router->resource('new-confirm-case-models', NewConfirmCaseModelController::class);
    $router->resource('comments', CaseModelCommentsController::class);
    $router->resource('gens', GenController::class);

    $router->get('forms/settings', 'FormController@settings');
    $router->resource('suspect-court-statuses', SuspectCourtStatusController::class);
    $router->resource('detection-methods', DetectionMethodController::class);
    $router->resource('arresting-agencies', ArrestingAgencyController::class);

    $router->resource('suspect-links', SuspectLinkController::class);
});
