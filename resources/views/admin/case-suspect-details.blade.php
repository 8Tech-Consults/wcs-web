<?php
use App\Models\Utils;
?>
<style>
    .my-table th {
        border: 2px solid black !important;
    }

    .my-table td,
    .my-table tr {
        border: 2px solid rgb(184, 203, 204) !important;
    }
</style>
<div class="container bg-white p-1 p-md-5">
    <div class="d-md-flex justify-content-between">
        <div>
            <h2 class="m-0 p-0 text-dark h3 text-uppercase"><b>Suspect {{ ' - ' . $s->uwa_suspect_number ?? '-' }}</b>
            </h2>
        </div>
        <div class="mt-3 mt-md-0">
            @isset($_SERVER['HTTP_REFERER'])
                <a href="{{ $_SERVER['HTTP_REFERER'] }}" class="btn btn-secondary btn-sm"><i class="fa fa-chevron-left"></i>
                    BACK
                    TO ALL SUSPECTS</a>
            @endisset
            <a href="#" onclick="window.print();return false;" class="btn btn-primary btn-sm"><i
                    class="fa fa-print"></i> PRINT</a>
        </div>
    </div>
    <hr class="my-3 my-md-4">
    <div class="row">
        <div class="col-3 col-md-2">
            <div class="border border-1 rounded bg-">
                <img class="img-fluid" src="{{ $s->photo_url }}">
            </div>
        </div>
        <div class="col-9 col-md-5">
            <h3 class="text-uppercase h4 p-0 m-0"><b>BIO DATA</b></h3>
            <hr class="my-1 my-md-3">

            @include('components.detail-item', [
                't' => 'name',
                's' => $s->first_name . ' ' . $s->middle_name . ' ' . $s->last_name,
            ])
            @include('components.detail-item', ['t' => 'sex', 's' => $s->sex])
            @include('components.detail-item', [
                't' => 'Age',
                's' => $s->age,
            ])
            @include('components.detail-item', ['t' => 'Phone number', 's' => $s->phone_number])
            @include('components.detail-item', [
                't' => 'National id number',
                's' => $s->national_id_number,
            ])

            @include('components.detail-item', [
                't' => 'Country of origin',
                's' => $s->country,
            ])

            @include('components.detail-item', [
                't' => 'Ethnicity',
                's' => $s->ethnicity,
            ])

            {{--   @include('components.detail-item', [
                't' => 'District, Sub-county',
                's' => $s->sub_county->name_text,
            ]) --}}



            @include('components.detail-item', [
                't' => 'Parish,Village',
                's' => $s->parish . ', ' . $s->village,
            ])



            @include('components.detail-item', [
                't' => 'REPORTed on DATE',
                's' => Utils::my_date($s->created_at),
            ])
            @include('components.detail-item', [
                't' => 'UWA SUSPECT',
                's' => $s->uwa_suspect_number,
            ])

            @include('components.detail-item', ['t' => 'OCCUPATION', 's' => $s->occuptaion])
        </div>
        <div class="pt-3 pt-md-0 col-md-5">
            <div class=" border border-primary p-3">
                <h3 class="text-uppercase h4 p-0 m-0 text-center"><b>Summary</b></h3>
                <hr class="border-primary mt-3">
                <div style="font-family: monospace; font-size: 16px;">
                    <p class="py-1 my-0 "><b>CASE DATE:</b>
                        {{ Utils::to_date_time($s->case->case_date) }}</p>
                    <p class="py-1 my-0 "><b>CASE TITLE:</b> <a href="{{ admin_url('cases/' . $s->case->id) }}"><span
                                class="text-primary"
                                title="View case details">{{ $s->case->title ?? $s->case->id }}</span></a>
                    </p>
                    <p class="py-1 my-0 "><b>CASE NUMBER:</b> {{ $s->case->case_number }}</p>


                    <p class="py-1 my-0"><b class="text-uppercase">CASE suspetcs:</b> {{ count($s->case->suspects) }}
                    </p>

                    <p class="py-1 my-0 "><b class="text-uppercase">Case committed in PA?:</b>
                        {{ $s->case->is_offence_committed_in_pa }}
                    </p>

                    <p class="py-1 my-0 "><b class="text-uppercase">PA:</b>
                        {{ $s->case->pa->name_text }} </p>

                    @if ($s->case->is_offence_committed_in_pa == 'Yes' || $s->case->is_offence_committed_in_pa == '1')
                        <p class="py-1 my-0 "><b class="text-uppercase">Location:</b>
                            {{ $s->case->village }} </p>
                    @else
                        <p class="py-1 my-0 "><b class="text-uppercase">CASE district:</b>
                            {{ Utils::get('App\Models\Location', $s->case->district_id)->name_text }} </p>


                        <p class="py-1 my-0 "><b class="text-uppercase">CASE Sub-county:</b>
                            {{ Utils::get('App\Models\Location', $s->case->sub_county_id)->name_text }} </p>
                    @endif
                    <p class="py-1 my-0 "><b class="text-uppercase">Reporter:</b>
                        {{ $s->case->reportor->name }} </p>


                </div>
            </div>
        </div>
    </div>

    @php
        $otherCases = $s->otherCasese();
    @endphp
    <hr class="mt-4 mb-2 border-primary pb-0 mt-md-5 mb-md-5">
    <h3 class="text-uppercase h4 p-0 m-0 text-center"><b>Other cases associated with suspect</b></h3>
    <hr class="m-0 pt-2 mt-2 mb-3">
    @if ($otherCases->count() == 0)
        <div class="alert alert-info">
            <p class="text-center">No other cases associated with this suspect</p>
        </div>
    @else
        <ul>
            @foreach ($otherCases as $_item)
                <li><b>{{ $_item->case->title }}</b> - {{ $_item->case->case_number }}
                    - <b><a target="_blank" href="{{ admin_url('case-suspects/' . $_item->id) }}"><span
                                class="text-primary" title="View This Case Details">VIEW THIS CASE
                                DETAILS</span></a></b>
                </li>
            @endforeach
        </ul>
    @endif


    <hr class="mt-4 mb-2 border-primary pb-0 mt-md-5 mb-md-5">
    <h3 class="text-uppercase h4 p-0 m-0 text-center"><b>Offences Committed</b></h3>
    <hr class="m-0 pt-0 mb-3">
    <ul>
        @foreach ($s->offences as $item)
            <li><b>{{ $item->name }}</b></li>
        @endforeach
    </ul>

    <hr class="mt-4 mb-2 border-primary pb-0 mt-md-5 mb-md-5">
    <h3 class="text-uppercase h4 p-0 m-0 text-center"><b>ARREST information</b></h3>
    <hr class="m-0 pt-0">

    @if ($s->is_suspects_arrested == 1 || $s->is_suspects_arrested == 'Yes')
        <div class="row pt-2">
            <div class="col-md-6 pl-5 pl-md-5">
                @include('components.detail-item', [
                    't' => 'Has suspect been handed over to police?',
                    's' => $s->is_suspects_arrested,
                ])

                @include('components.detail-item', [
                    't' => 'Arrest date',
                    's' => Utils::my_date($s->arrest_date_time),
                ])

                @include('components.detail-item', [
                    't' => 'Arrest in P.A',
                    's' => $s->arrest_in_pa,
                ])
                @include('components.detail-item', [
                    't' => 'P.A of Arrest',
                    's' => $s->arrestPa->name,
                ])

                @include('components.detail-item', [
                    't' => 'C.A',
                    's' => $s->arrestCa->name,
                ])

                @if($s->arrest_in_pa == 'Yes' || $s->arrest_in_pa == '1')    
                    @include('components.detail-item', [
                        't' => 'Arrest Location',
                        's' => $s->arrest_village,
                    ])
                    @php
                        $s->arrest_village ='- ';
                    @endphp

                @else 
                    @include('components.detail-item', [
                        't' => 'Arrest Location',
                        's' => '-',
                    ])
                @endif

                @include('components.detail-item', [
                    't' => 'District',
                    's' => Utils::get('App\Models\Location', $s->arrest_district_id)->name,
                ])
                @include('components.detail-item', [
                    't' => 'Sub-county',
                    's' => Utils::get('App\Models\Location', $s->arrest_sub_county_id)->name,
                ])
                @include('components.detail-item', [
                    't' => 'Arrest parish',
                    's' => $s->arrest_parish,
                ])

            </div>
            <div class="col-md-6 border-left pl-2 pl-5">
                    
                @include('components.detail-item', [
                    't' => 'Arrest village',
                    's' => $s->arrest_village,
                ])

                @include('components.detail-item', [
                    't' => 'Arrest GPS',
                    's' => $s->arrest_latitude . ',' . $s->arrest_longitude,
                ])
                @include('components.detail-item', [
                    't' => 'First police station',
                    's' => $s->arrest_first_police_station,
                ])
                @include('components.detail-item', [
                    't' => 'Current police station',
                    's' => $s->arrest_current_police_station,
                ])
                @include('components.detail-item', [
                    't' => 'Lead Arrest agency',
                    's' => $s->arrest_agency,
                ])
                
                @include('components.detail-item', [
                    't' => 'Other Arrest agencies',
                    's' => $other_arrest_agencies,
                ])
                @include('components.detail-item', [
                    't' => 'UWA Arrest unit',
                    's' => $s->arrest_uwa_unit,
                ])

                @include('components.detail-item', [
                    't' => 'Arrest CRB number',
                    's' => $s->arrest_crb_number,
                ])
                @include('components.detail-item', [
                    't' => 'Police SD number',
                    's' => $s->police_sd_number,
                ])


            </div>
        </div>
    @else
        @include('components.detail-item', [
            't' => 'Has suspect been handed over to police?',
            's' => $s->is_suspects_arrested,
        ])
        @if($s->is_suspects_arrested == 'No')
            @include('components.detail-item', [
                't' => 'Action taken by management',
                's' => $s->management_action,
            ])
            @include('components.detail-item', [
                't' => 'Remarks by management',
                's' => $s->not_arrested_remarks,
            ])
        @endif
    @endif




    <hr class="mt-4 mb-2 border-primary pb-0 mt-md-5 mb-md-5">
    <h3 class="text-uppercase h4 p-0 m-0 text-center"><b>Court information</b></h3>
    <hr class="m-0 pt-0">
    @if ($s->is_suspect_appear_in_court == '1' || $s->is_suspect_appear_in_court == 'Yes')
        <div class="row pt-2">
            <div class="col-md-6 pl-5 pl-md-5">

                @include('components.detail-item', [
                    't' => 'Has this suspect appeared in court?',
                    's' => $s->is_suspect_appear_in_court,
                ])
                @include('components.detail-item', [
                    't' => 'Court File Number',
                    's' => $s->court_file_number,
                ])
                @include('components.detail-item', [
                    't' => 'Court date',
                    's' => Utils::my_date($s->court_date),
                ])


                <?php
                $court_name = '';
                if ($s->court != null) {
                    $court_name = $s->court->name;
                }
                ?>
                @include('components.detail-item', [
                    't' => 'Court name',
                    's' => $court_name,
                ])
                @include('components.detail-item', [
                    't' => 'Lead prosecutor',
                    's' => $s->prosecutor,
                ])
                @include('components.detail-item', [
                    't' => 'Court Magistrate',
                    's' => $s->magistrate_name,
                ])
                @include('components.detail-item', [
                    't' => 'Court case status',
                    's' => $s->court_status,
                ])
                @if($s->court_status != 'Concluded') 
                    @include('components.detail-item', [
                        't' => 'Accused court status',
                        's' => $s->suspect_court_outcome,
                    ])                
                @endif
          
                @include('components.detail-item', [
                    't' => 'Specific court case status',
                    's' => $s->case_outcome,
                ])
                
                @includeWhen(in_array($s->case_outcome, ['Dismissed', 'Withdrawn by DPP', 'Acquittal']), 'components.detail-item', [
                    't' => 'Specific court case status remarks',
                    's' => $s->case_outcome_remarks,
                ])

                @include('components.detail-item', [
                    't' => 'Jailed',
                    's' => $s->is_jailed == '1' || $s->is_jailed == 'Yes' ? 'Yes' : 'No',
                ])

                @include('components.detail-item', [
                    't' => 'Jail date',
                    's' => Utils::my_date($s->jail_date),
                ])

                @include('components.detail-item', [
                    't' => 'Jail period',
                    's' => $s->jail_period,
                ])
                @include('components.detail-item', [
                    't' => 'Prison',
                    's' => $s->prison,
                ])




            </div>
            <div class="col-md-6 border-left pl-2 pl-5">

                @include('components.detail-item', [
                    't' => 'Date release',
                    's' => Utils::my_date($s->jail_release_date),
                ])

                @include('components.detail-item', [
                    't' => 'Is fined',
                    's' => $s->is_fined == '1' || $s->is_fined == 'Yes' ? 'Yes' : 'No',
                ])

                @include('components.detail-item', [
                    't' => 'Fined amount',
                    's' => $s->fined_amount,
                ])

                @include('components.detail-item', [
                    't' => 'Community service',
                    's' => $s->community_service,
                ])
                @include('components.detail-item', [
                    't' => 'Community service Duration (in hours)',
                    's' => $s->community_service_duration,
                ])

                @include('components.detail-item', [
                    't' => 'Cautioned',
                    's' => $s->cautioned == '1' || $s->cautioned == 'Yes' ? 'Yes' : 'No',
                ])

                @include('components.detail-item', [
                    't' => 'Cautioned remarks',
                    's' => $s->cautioned_remarks,
                ])

                @include('components.detail-item', [
                    't' => 'Accused appealed',
                    's' => $s->suspect_appealed == '1' || $s->suspect_appealed == 'Yes' ? 'Yes' : 'No',
                ])
                @include('components.detail-item', [
                    't' => 'Appeal date',
                    's' => Utils::my_date($s->suspect_appealed_date),
                ])
                @include('components.detail-item', [
                    't' => 'Appellate court name',
                    's' => $s->suspect_appealed_court_name,
                ])
                @include('components.detail-item', [
                    't' => 'Appeal court file number',
                    's' => $s->suspect_appealed_court_file,
                ])
                @include('components.detail-item', [
                    't' => 'Appeal outcome',
                    's' => $s->suspect_appealed_outcome,
                ])

                @include('components.detail-item', [
                    't' => 'Appeal remarks',
                    's' => $s->suspect_appeal_remarks,
                ])



            </div>
        </div>
    @else
        @include('components.detail-item', [
            't' => 'Has this suspect appeared in court?',
            's' => $s->is_suspect_appear_in_court,
        ])


        @if ($s->is_suspects_arrested == 1 || $s->is_suspects_arrested == 'Yes')
            @include('components.detail-item', [
                't' => 'Case outcome at police level',
                's' => $s->police_action,
            ])

            @include('components.detail-item', [
                't' => 'Remarks by Police',
                's' => $s->police_action_remarks,
            ])
        @endif
    @endif




    <hr class="my-5">
    <h3 class="text-uppercase h4 p-0 m-0 mb-2 text-center  mt-3 mt-md-5"><b>Offence Exhibits</b></h3>
    <div class="row">
        <div class="col-12">
            <table class="table table-striped table-hover my-table"">
                <thead class="bg-primary">
                    <tr>
                        {{-- <th scope="col">ID</th> --}}
                        <th scope="col">Photos</th>
                        <th scope="col">Category</th>
                        <th scope="col">Quantity (KGs) & No. of Pieces</th>
                        <th scope="col">Description</th>
                        {{--                         <th scope="col">Action</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach ($s->case->exhibits as $e)
                        @include('components/exhibit-item', ['e' => $e])
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>



    <hr class="my-5">
    <h3 class="text-uppercase h4 p-0 m-0 mb-2 text-center  mt-3 mt-md-5"><b>Other suspects involved in this case</b>
    </h3>

    @include('admin/section-suspects', ['items' => $s->case->suspects])



</div>
<style>
    .content-header {
        display: none;
    }
</style>
