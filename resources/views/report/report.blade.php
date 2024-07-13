<?php
/* import casemodel */
use App\Models\CaseModel;
/* import suspectmodel */
use App\Models\SuspectModel;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $report->title ?></title>
    @include('report.css')
    <style>
        /* margin */
        body {
            margin: 0 !important;
            padding: 0 !important;
            margin-top: 15 !important;
        }
    </style>
    <style>
        .label {
            font-size: 10px;
            font-weight: bold;
            color: #6c757d;
            line-height: 12px;
            padding: 0;
            margin: 0;
            padding-bottom: 2px;
        }

        .value {
            font-size: 14px;
            font-weight: bold;
            color: black;
            line-height: 13px;
            padding: 0;
            margin: 0;
            padding-bottom: 5px;
        }
    </style>

</head>

<body>

    <div style="display: flex;">
        <div
            style="display: inline-block; 
        height: 80px;
        background-color: rgb(54, 51, 51);
        width: 45px; 
        ">
        </div>
        <div
            style="display: inline-block; 
        height: 80px;
        background-color: rgb(12, 131, 12);
        width: 5px; 
        ">
        </div>
        <div style="margin-left: 0px; display: inline-block; width: 90%">
            <span class=""
                style="
                letter-spacing: 0.02px;
                line-height: 52px; 
                font-size: 45px;">OWODAT
                DATA ANALYSIS</span>
            <span class="pl-3 text-white py-1 fs-18 d-block  mt-0"
                style="background-color: rgb(12, 131, 12);">TITLE</span>
        </div>
    </div>

    <table style="margin-top: 50px;">
        <tr>
            <td style="width: 52px"></td>
            <td>
                <h1 class="text-uppercase text-left fs-24 " style=" line-height: 27px; letter-spacing: 0.1px;">
                    {{ $report->title }}.</h1>
                <hr
                    style="
                background-color: black;
                height: 6px;
                margin-bottom: 2px;
                margin-top: 15px;
                ">

                <hr
                    style="
                 padding: 0%;
                 margin: 0%;
                 background-color: rgb(12, 131, 12);
                 height: 2px;
                 ">
                <p
                    style="
                margin-top: 15px;
                font-size: 14px;
                line-height: 18px;
                text-align: justify;
                ">
                    The OWODAT (Offender Wildlife Database) report provides a comprehensive analysis of wildlife-related
                    offenses in the country, focusing on cases registered across various conservation and protected
                    areas. This report aggregates data on wildlife crimes, including the number of cases, suspects, and
                    specific wildlife species affected.</p>

                <div
                    style="
                         widows: 100%;
                        margin-top: 15px;
                        height: 380px;
                        background-color: rgb(12, 131, 12);
                        background-image: url('<?= public_path('') ?>/assets/bg/<?= rand(1, 16) ?>-min.jpg');
                        background-size: cover;
                        background-repeat: no-repeat;
                        background-position: center;
                ">
                </div>
                <h2 class="text-uppercase text-left fs-24 mt-4"
                    style=" line-height: 27px; letter-spacing: 0.1px;
                underline: 1px solid black; 
                /* undeline style as double */
                text-decoration: underline double black;

                ">
                    <u>Overview</u>
                </h2>
                <p
                    style="
            margin-top: 16px;
            font-size: 14px;
            line-height: 18px;
            text-align: justify;
            ">
                    This report aims to give an overarching view of wildlife crime trends and enforcement actions,
                    highlighting
                    critical statistics on offenses involving elephants, pangolins, and other key species. By analyzing
                    the data dynamically through filters such as Conservation Area, Protected Area, and Case Date, this
                    report offers valuable insights to aid in the effective management and protection of the country's
                    wildlife resources.
                </p>
                {{-- <ul>
                    <li>
                        <span class="label">1.0</span>
                        <span class="value">INT RODUCTION</span>
                    </li>
                    <li>
                        <span class="label">2.0</span>
                        <span class="value">OBJECTIVES</span>
                    </li>
                    <li>
                        <span class="label">3.0</span>
                        <span class="value">METHODOLOGY</span>
                    </li>
                </ul> --}}

            </td>
        </tr>
    </table>

    <hr style="
background-color: black;
height: 6px;
margin-bottom: 2px;
margin-top: 0px;
">

    <hr style="
 padding: 0%;
 margin: 0%;
 background-color: rgb(12, 131, 12);
 height: 2px;
 margin-bottom: 5px;
 ">
    <p class="text-center p-0 m-0 text-uppercase text-muted ">Powored By</p>
    <center>
        <img src="<?= public_path('') ?>/assets/logos.png" style="width: 250px; " alt="">
    </center>



    <article class="fs-18">
        <h2 class="fs-26"><u>Table of Contents</u></h2>
        <ul>
            <li>
                <span class="muted">1.0</span>
                &nbsp;&nbsp;
                <span class="">Summary</span>
            </li>
            <li>
                <span class="muted">2.0</span>
                &nbsp;&nbsp;
                <span class="">Cases and Suspects</span>
            </li>
            <li>
                <span class="muted">3.0</span>
                &nbsp;&nbsp;
                <span class="">POLICE ARREST INFORMATION</span>
            </li>
            <li>
                <span class="muted">4.0</span>
                &nbsp;&nbsp;
                <span class="">COURT REPORT</span>
            </li>
            <li>
                <span class="muted">5.0</span>
                &nbsp;&nbsp;
                <span class="">WILDLIFE SPECIES AFFECTED</span>
            </li>
            <li>
                <span class="muted">6.0</span>
                &nbsp;&nbsp;
                <span class="">HUNTING IMPLEMENTS</span>
            </li>
        </ul>
    </article>
    <article>
        @include('report.widget-title', [
            'title' => 'Summary',
            'number' => '1.0',
            'report' => $report,
        ])

        <div style="display: flex; margin-top: 50px; justify-content: space-between; margin-top: 65px;">
            <div style="display: inline-block; width: 30%; border-radius: 15px; ">
                <div class="border border-success rounded-5 text-center p-3 "
                    style="border-radius: 20px!important; border-width: 8px!important;">
                    <p class="fs-50 "><?= number_format($report->cases_count) ?></p>
                    <p class="fs-18 text-uppercase" style="font-weight: 900">Total Number of Cases</p>
                </div>
            </div>

            <div style="margin-left: 15px; display: inline-block; width: 30%; border-radius: 15px;">
                <div class="border border-success rounded-5 text-center p-3 "
                    style="border-radius: 20px!important; border-width: 8px!important;">
                    <p class="fs-50 "><?= number_format($report->suspects_count) ?></p>
                    <p class="fs-18 text-uppercase" style="font-weight: 900">Total Number of Suspects</p>
                </div>
            </div>

            <div style="margin-left: 15px; display: inline-block; width: 30%;">
                <div class="border border-success rounded-5 text-center p-3 "
                    style="border-radius: 20px!important; border-width: 8px!important;">
                    <p class="fs-50 "><?= number_format($report->exhibits_count) ?></p>
                    <p class="fs-18 text-uppercase" style="font-weight: 900">Total Number of Exhibits</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 30%; vertical-align:top" class="">
                            <h2 class="fs-20 text-uppercase">TOP 10 C.As </h2>
                            <hr
                                style="
                                    background-color: black;
                                    height: 5px;
                                    margin-bottom: 2px;
                                    margin-top: 0px;
                                    ">

                            <hr
                                style="
                                    padding: 0%;
                                    margin: 0%;
                                    background-color: rgb(12, 131, 12);
                                    height: 2px;
                                    ">
                            {{-- check if $report->top_conservation_areas is empty --}}
                            @if (count($report->top_conservation_areas) < 1)
                                <p>No data available</p>
                            @else
                                <ol class="p-0 m-0" style="padding-left: 20px!important;">
                                    @foreach ($report->top_conservation_areas as $conservation_area)
                                        <li>
                                            <p class="p-0 m-0 mb-1">{{ $conservation_area['name'] }}:
                                                <b>{{ number_format($conservation_area['total']) }}</b>
                                                Cases.
                                            </p>
                                        </li>
                                    @endforeach
                                </ol>
                            @endif
                        </td>
                        <td style="width: 30%; vertical-align:top">
                            <h2 class="fs-20 text-uppercase">TOP 10 P.As</h2>
                            <hr
                                style="
                                    background-color: black;
                                    height: 5px;
                                    margin-bottom: 2px;
                                    margin-top: 0px;
                                    ">

                            <hr
                                style="
                                    padding: 0%;
                                    margin: 0%;
                                    background-color: rgb(12, 131, 12);
                                    height: 2px;
                                    ">
                            {{-- do the same for top_protected_areas --}}
                            @if (count($report->top_protected_areas) < 1)
                                <p>No data available</p>
                            @else
                                <ol class="p-0 m-0" style="padding-left: 20px!important;">
                                    @foreach ($report->top_protected_areas as $protected_area)
                                        <li>
                                            <p class="p-0 m-0 mb 1">{{ $protected_area['name'] }}:
                                                <b>{{ number_format($protected_area['total']) }}</b>
                                                Cases.
                                            </p>
                                        </li>
                                    @endforeach
                                </ol>
                            @endif
                        </td>
                        <td style="width: 30%; vertical-align:top">
                            <h2 class="fs-20 text-uppercase">Top 10 Species</h2>
                            <hr
                                style="
                                    background-color: black;
                                    height: 5px;
                                    margin-bottom: 2px;
                                    margin-top: 0px;
                                    ">

                            <hr
                                style="
                                    padding: 0%;
                                    margin: 0%;
                                    background-color: rgb(12, 131, 12);
                                    height: 2px;
                                    ">
                            {{-- do the same for top_exhibits --}}
                            @if (count($report->top_exhibits) < 1)
                                <p>No data available</p>
                            @else
                                <ol class="p-0 m-0" style="padding-left: 20px!important;">
                                    @foreach ($report->top_exhibits as $exhibit)
                                        <li>
                                            <p class="p-0 m-0 mb 1">{{ $exhibit['name'] }}:
                                                <b>{{ number_format($exhibit['total']) }}</b>
                                                Cases.
                                            </p>
                                        </li>
                                    @endforeach
                                </ol>
                            @endif

                        </td>

                    </tr>
                </table>
            </div>
        </div>
    </article>

    <article>
        @include('report.widget-title', [
            'title' => 'Cases and Suspects',
            'number' => '2.0',
            'report' => $report,
        ])

        <table style="width: 100%;">
            <tr>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of cases associated with PAs',
                        'number' => $report->get_pa_cases(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of suspects associated with PAs',
                        'number' => $report->get_pa_suspects(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of cases not associated with PAs',
                        'number' => $report->get_non_pa_cases(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of suspects not associated with PAs',
                        'number' => $report->get_non_pa_suspects(),
                    ])
                </td>

            </tr>
            <tr>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of male suspects',
                        'number' => $report->get_male_suspects(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of female suspects',
                        'number' => $report->get_female_suspects(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of suspects below 18 years',
                        'number' => $report->get_under_18_suspects(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of suspects above 60 years',
                        'number' => $report->get_above_60_suspects(),
                    ])
                </td>

            </tr>

            <tr>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of cases handled at UWA management',
                        'number' => $report->get_uwa_cases(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of suspects fined by UWA management',
                        'number' => $report->get_fined_suspects(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of suspects below 18 years',
                        'number' => $report->get_cautioned_suspects(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of suspects cautioned and released by UWA management',
                        'number' => $report->get_at_large_suspects(),
                    ])
                </td>

            </tr>
        </table>
    </article>
    <article>
        @include('report.widget-title', [
            'title' => 'Police arrest report',
            'number' => '3.0',
            'report' => $report,
        ])

        <table style="width: 100%;">
            <tr>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of cases registered at police',
                        'number' => $report->get_police_cases(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of suspects handled over to police',
                        'number' => $report->get_police_suspects(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of suspects in police custody',
                        'number' => $report->get_police_custody_suspects(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of suspects released on police bond',
                        'number' => $report->get_police_bond_suspects(),
                    ])
                </td>
            </tr>
            <tr>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of suspects that have skipped bond',
                        'number' => $report->get_skipped_bond_suspects(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of suspects that have escaped from police custody',
                        'number' => $report->get_escaped_suspects(),
                    ])
                </td>
            </tr>
        </table>
    </article>

    <article>
        @include('report.widget-title', [
            'title' => 'COURT REPORT',
            'number' => '4.0',
            'report' => $report,
        ])

        <table style="width: 100%;">
            <tr>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of cases forwarded to court',
                        'number' => $report->get_forwarded_cases(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of suspects forwarded to court',
                        'number' => $report->get_forwarded_suspects(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of cases undergoing prosecution',
                        'number' => $report->get_ongoing_prosecution_cases(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of accused persons undergoing prosecution',
                        'number' => $report->get_ongoing_prosecution_suspects(),
                    ])
                </td>
            </tr>

            <tr>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of accused persons on court bail',
                        'number' => $report->get_court_bail_suspects(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of accused persons who jumped court bail',
                        'number' => $report->get_jumped_bail_suspects(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of cases undergoing prosecution',
                        'number' => $report->get_ongoing_prosecution_cases(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of concluded cases',
                        'number' => $report->get_concluded_cases(),
                    ])
                </td>
            </tr>

            <tr>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of dismissed cases',
                        'number' => $report->get_dismissed_cases(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of accused persons convicted',
                        'number' => $report->get_convicted_suspects(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of convicts jailed',
                        'number' => $report->get_jailed_convicts(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of convicts fined',
                        'number' => $report->get_fined_convicts(),
                    ])
                </td>
            </tr>


            <tr>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of convicts that were given a community service',
                        'number' => $report->get_community_service_convicts(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of accused persons that were cautioned',
                        'number' => $report->get_cautioned_convicts(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of accused persons that were Acquitted',
                        'number' => $report->get_acquitted_convicts(),
                    ])
                </td>
            </tr>

        </table>
    </article>


    <article>
        @include('report.widget-title', [
            'title' => 'WILDLIFE SPECIES',
            'number' => '5.0',
            'report' => $report,
        ])

        <table style="width: 100%;">
            <tr>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of cases involving Elephants',
                        'number' => $report->get_elephant_cases(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Quantity in Kgs of Elephant Ivory',
                        'number' => $report->get_elephant_ivory_kgs(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of cases involving Pangolins',
                        'number' => $report->get_pangolin_cases(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of live pangolins',
                        'number' => $report->get_live_pangolins(),
                    ])
                </td>
            </tr>
            <tr>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Quantity in Kgs of Pangolin scales',
                        'number' => $report->get_pangolin_scales_kgs(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of cases involving Hippopotamus',
                        'number' => $report->get_hippopotamus_cases(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Quantity in Kgs of Hippo Teeth',
                        'number' => $report->get_hippo_teeth_kgs(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of cases involving bushmeat',
                        'number' => $report->get_bushmeat_cases(),
                    ])
                </td>
            </tr>
            <tr>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Quantity in Kgs of bushmeat',
                        'number' => $report->get_bushmeat_kgs(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of cases involving Lions',
                        'number' => $report->get_lion_cases(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of cases involving Leopards',
                        'number' => $report->get_leopard_cases(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of cases involving Gorillas',
                        'number' => $report->get_gorilla_cases(),
                    ])
                </td>
            </tr>
            <tr>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of cases involving Chimpanzees',
                        'number' => $report->get_chimpanzee_cases(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of cases involving Giraffes',
                        'number' => $report->get_giraffe_cases(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of cases involving Uganda Kobs',
                        'number' => $report->get_uganda_kob_cases(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of cases involving Buffalos',
                        'number' => $report->get_buffalo_cases(),
                    ])
                </td>
            </tr>
            <tr>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of cases involving Rhinos',
                        'number' => $report->get_rhino_cases(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of cases involving Parrots',
                        'number' => $report->get_parrot_cases(),
                    ])

                </td>
            </tr>
        </table>
    </article>

    <article>
        @include('report.widget-title', [
            'title' => 'HUNTING IMPLEMENTS',
            'number' => '5.0',
            'report' => $report,
        ])

        <table style="width: 100%;">
            <tr>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of cases involving wire snares',
                        'number' => $report->get_wire_snare_cases(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of wire snares',
                        'number' => $report->get_wire_snare_pieces(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of cases involving guns',
                        'number' => $report->get_gun_cases(),
                    ])
                </td>
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of guns',
                        'number' => $report->get_gun_pieces(),
                    ])
                </td>
            </tr>
            <tr>
                {{-- get_ammunition_cases --}}
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of cases involving ammunition',
                        'number' => $report->get_ammunition_cases(),
                    ])
                </td>
                {{-- get_ammunition_pieces --}}
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of ammunition',
                        'number' => $report->get_ammunition_pieces(),
                    ])
                </td>
                {{-- get_spear_cases --}}
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of cases involving spears',
                        'number' => $report->get_spear_cases(),
                    ])
                </td>
                {{-- get_spear_pieces --}}
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of spears',
                        'number' => $report->get_spear_pieces(),
                    ])
                </td>
            </tr>
            <tr>
                {{-- get_panga_cases --}}
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of cases involving pangas',
                        'number' => $report->get_panga_cases(),
                    ])
                </td>
                {{-- get_panga_pieces --}}
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of pangas',
                        'number' => $report->get_panga_pieces(),
                    ])
                </td>
                {{-- get_arrow_cases --}}
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of cases involving arrows',
                        'number' => $report->get_arrow_cases(),
                    ])
                </td>
                {{-- get_arrow_pieces --}}
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of arrows',
                        'number' => $report->get_arrow_pieces(),
                    ])
                </td>
            </tr>
            <tr>
                {{-- get_metal_trap_cases --}}
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of cases involving metal traps',
                        'number' => $report->get_metal_trap_cases(),
                    ])
                </td>
                {{-- get_metal_trap_pieces --}}
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of metal traps',
                        'number' => $report->get_metal_trap_pieces(),
                    ])
                </td>
                {{-- get_knife_cases --}}
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of cases involving knives',
                        'number' => $report->get_knife_cases(),
                    ])
                </td>
                {{-- get_knife_pieces --}}
                <td style="width: 30%">
                    @include('report.widget-data-item', [
                        'title' => 'Number of knives',
                        'number' => $report->get_knife_pieces(),
                    ])
                </td>
            </tr>
        </table>


</body>

</html>
