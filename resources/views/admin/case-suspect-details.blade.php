<?php
use App\Models\Utils;
?><div class="container bg-white p-1 p-md-5">
    <div class="d-md-flex justify-content-between">
        <div>
            <h2 class="m-0 p-0 text-dark h3 text-uppercase"><b>Suspect {{ '#' . $s->id ?? '-' }}</b></h2>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ url('case-suspects') }}" class="btn btn-secondary btn-sm"><i class="fa fa-chevron-left"></i> BACK
                TO ALL SUSPECTS</a>
            <a href="{{ url('case-suspects/' . $s->id . '/edit') }}" class="btn btn-warning btn-sm"><i
                    class="fa fa-edit"></i>
                EDIT</a>
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
                't' => 'Date of birth',
                's' => Utils::my_date($s->age),
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

            @include('components.detail-item', [
                't' => 'District, Sub-county',
                's' => $s->sub_county->name_text,
            ])



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

            @include('components.detail-item', ['t' => 'occuptaion', 's' => $s->occuptaion])
        </div>
        <div class="pt-3 pt-md-0 col-md-5">
            <div class=" border border-primary p-3">
                <h3 class="text-uppercase h4 p-0 m-0 text-center"><b>Summary</b></h3>
                <hr class="border-primary mt-3">
                <div style="font-family: monospace; font-size: 16px;">
                    <p class="py-1 my-0 "><b>OFFENCE DATE:</b>
                        {{ Utils::to_date_time($s->case->created_at) }}</p>
                    <p class="py-1 my-0 "><b>OFFENCE TITLE:</b> <a
                            href="{{ admin_url('cases/' . $s->case->id) }}">{{ $s->case->title ?? $s->case->id }}</a>
                    </p>
                    <p class="py-1 my-0"><b>OFFENCE STATUS:</b> <span
                            class="badge bg-{{ Utils::tell_case_status_color($s->case->status) }}">
                            {{ Utils::tell_case_status($s->case->status) ?? '-' }} </span></p>

                    <p class="py-1 my-0"><b class="text-uppercase">CASE suspetcs:</b> {{ count($s->case->suspects) }}
                    </p>

                    <p class="py-1 my-0 "><b class="text-uppercase">CASE district:</b>
                        {{ Utils::get('App\Models\Location', $s->case->district_id)->name_text }} </p>

                    <p class="py-1 my-0 "><b class="text-uppercase">CASE sub-county:</b>
                        {{ Utils::get('App\Models\Location', $s->case->sub_county_id)->name_text }} </p>

                    <p class="py-1 my-0 "><b class="text-uppercase">Case Parish:</b>
                        {{ $s->case->parish }} </p>

                    <p class="py-1 my-0 "><b class="text-uppercase">Case village:</b>
                        {{ $s->case->village }} </p>

                    <p class="py-1 my-0 "><b class="text-uppercase">Reporter:</b>
                        {{ $s->case->reportor->name }} </p>


                </div>
            </div>
        </div>
    </div>

    <hr class="mt-4 mb-2 border-primary pb-0 mt-md-5 mb-md-5">
    <h3 class="text-uppercase h4 p-0 m-0 text-center"><b>ARREST information</b></h3>
    <hr class="m-0 pt-0">

    @if ($s->is_suspects_arrested)
        <div class="row pt-2">
            <div class="col-md-6 pl-5 pl-md-5">
                @include('components.detail-item', [
                    't' => 'Arrest date',
                    's' => Utils::my_date($s->arrest_date_time),
                ])
                @include('components.detail-item', [
                    't' => 'Arrest district',
                    's' => Utils::get('App\Models\Location', $s->arrest_district_id)->name_text,
                ])
                @include('components.detail-item', [
                    't' => 'Arrest sub-county',
                    's' => Utils::get('App\Models\Location', $s->arrest_sub_county_id)->name_text,
                ])
                @include('components.detail-item', [
                    't' => 'Arrest parish',
                    's' => $s->arrest_parish,
                ])
                @include('components.detail-item', [
                    't' => 'Arrest village',
                    's' => $s->arrest_village,
                ])

                @include('components.detail-item', [
                    't' => 'UWA UNIT',
                    's' => $s->arrest_uwa_unit,
                ])

            </div>
            <div class="col-md-6 border-left pl-2 pl-5">


                @include('components.detail-item', [
                    't' => 'UWA Arrest NUMBER',
                    's' => $s->arrest_uwa_number,
                ])

                @include('components.detail-item', [
                    't' => 'CRB NUMBER',
                    's' => $s->arrest_crb_number,
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
                    't' => 'Detection method',
                    's' => $s->arrest_detection_method,
                ])

                @include('components.detail-item', [
                    't' => 'Arrest time',
                    's' => Utils::to_date_time($s->arrest_date_time),
                ])

                @include('components.detail-item', [
                    't' => 'Arrest GPS',
                    's' => Utils::get_gps_link($s->arrest_latitude, $s->arrest_longitude),
                ])
            </div>
        </div>
    @else
        <div class="alert alert-secondary mt-2">
            <p>This has not been arrested yet.</p>
        </div>
    @endif




    <hr class="mt-4 mb-2 border-primary pb-0 mt-md-5 mb-md-5">
    <h3 class="text-uppercase h4 p-0 m-0 text-center"><b>Court information</b></h3>
    <hr class="m-0 pt-0">
    @if ($s->is_suspect_appear_in_court)
        <div class="row pt-2">
            <div class="col-md-6 pl-5 pl-md-5">

                @include('components.detail-item', [
                    't' => 'Court name',
                    's' => $s->court_name,
                ])

                @include('components.detail-item', [
                    't' => 'Court file number',
                    's' => $s->court_file_number,
                ])

                @include('components.detail-item', [
                    't' => 'Magistrate name',
                    's' => $s->magistrate_name,
                ])

            </div>
            <div class="col-md-6 border-left pl-2 pl-5">

                @include('components.detail-item', [
                    't' => 'Prosecutor',
                    's' => $s->prosecutor,
                ])

                @include('components.detail-item', [
                    't' => 'Case outcome',
                    's' => $s->case_outcome,
                ])

                @include('components.detail-item', [
                    't' => 'Is convicted?',
                    's' => $s->is_convicted ? 'Yes' : 'No',
                ])



            </div>
        </div>
    @else
        <div class="alert alert-secondary mt-2">
            <p>This has not appeared in court yet.</p>
        </div>
    @endif



    <hr class="mt-4 mb-2 border-primary pb-0 mt-md-5 mb-md-5">
    <h3 class="text-uppercase h4 p-0 m-0 text-center"><b>Jail & Fine information</b></h3>
    <hr class="m-0 pt-0">
    @if ($s->is_jailed)
        <div class="row pt-2">
            <div class="col-md-6 pl-5 pl-md-5">

                @include('components.detail-item', [
                    't' => 'Is this suspect jailed?',
                    's' => $s->jail_period ? 'Yes' : 'No',
                ])
                @include('components.detail-item', [
                    't' => 'Jail period',
                    's' => $s->jail_period . 'Months',
                ])



            </div>
            <div class="col-md-6 border-left pl-2 pl-5">

                @include('components.detail-item', [
                    't' => 'Is this suspect jailed?',
                    's' => $s->is_fined ? 'Yes' : 'No',
                ])
                @include('components.detail-item', [
                    't' => 'Jail period',
                    's' => 'UGX ' . number_format(((int) $s->fined_amount)),
                ])

            </div>
        </div>
    @else
        <div class="alert alert-secondary mt-2">
            <p>This has not appeared in court yet.</p>
        </div>
    @endif

    <hr class="my-5">
    <h3 class="text-uppercase h3 p-0 m-0 mb-2 text-center  mt-3 mt-md-5"><b>Offence Exhibits</b></h3>
    <div class="row">
        <div class="col-12">
            <table class="table table-striped table-hover">
                <thead class="bg-primary">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Photo</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Catgory</th>
                        <th scope="col">Description</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($s->case->exhibits as $e)
                        <tr>
                            <th width="5%" scope="row">#{{ $e->id ?? '-' }}</th>
                            <td width="10%"><img class="border img-fluid rounded p-1" class="img-fluid"
                                    src="{{ url('assets/user.jpeg') ?? '-' }}"></td>
                            <td>{{ number_format((int) $e->id) ?? '-' }} KGs</td>
                            <td>{{ $e->exhibit_catgory ?? '-' }}</td>
                            <td>{{ $e->description ?? '-' }}</td>
                            <td width="20%">
                                <a class="text-primary" href="{{ admin_url() ?? '-' }}">See full details about this
                                    exhibit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>


    <hr class="my-5">
    <h3 class="text-uppercase h3 p-0 m-0 mb-2 text-center  mt-3 mt-md-5"><b>Other suspects involved in this case</b>
    </h3>

    @include('admin/section-suspects', ['items' => $s->case->suspects])



    <hr class="my-5">
    <h3 class="text-uppercase h3 p-0 m-0 mb-2 text-center  mt-3 mt-md-5"><b>Suspect Progress Comments</b></h3>



</div>
<style>
    .content-header {
        display: none;
    }
</style>
