<?php
use App\Models\Utils;
?><div class="container bg-white p-1 p-md-5">
    <div class="row">
        <div class="col-md-6">
            <h2 class="m-0 p-0 text-dark h3 text-uppercase"><b>Suspect {{ '#' . $s->id ?? '-' }} - details</b></h2>
        </div>
    </div>
    <hr class="my-3 my-md-4">
    <div class="row">
        <div class="col-3 col-md-2">
            <div class="border border-1 rounded bg-">
                <img class="img-fluid" src="{{ url('assets/user.jpeg') ?? '-' }}">
            </div>
        </div>
        <div class="col-9 col-md-6">
            <h3 class="text-uppercase h3 p-0 m-0">{{ $s->first_name . ' ' . $s->middle_name . ' ' . $s->last_name }}
            </h3>
            <hr class="my-1 my-md-3">

            @include('components.detail-item', ['t' => 'sex', 's' => $s->sex])
            @include('components.detail-item', [
                't' => 'Date of birth',
                's' => Utils::my_date($s->age),
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
        <div class="pt-3 pt-md-0 col-md-4">
            <div class=" border border-primary p-3">
                <h2 class="m-0 p-0 text-dark h3 text-center"><b>Suspect Summary</b></h2>
                <hr class="border-primary mt-3">
                <div style="font-family: monospace; font-size: 16px;">
                    <p class="py-1 my-0 text-uppercase"><b>CASE:</b> <a
                            href="{{ admin_url('cases/' . $s->case->id) }}">{{ $s->case->title ?? $s->case->id }}</a>
                    </p>
                    <p class="py-1 my-0"><b>STATUS:</b> {{ Utils::tell_case_status($s->status) ?? '-' }}</p>

                    <p class="py-1 my-0 text-uppercase"><b>Number of Suspects:</b> {{ '-' }}</p>


                </div>
            </div>
        </div>
    </div>

    <hr class="mt-4 mb-2 border-primary pb-0 mt-md-5 mb-md-5">
    <h3 class="text-uppercase h3 p-0 m-0 mb-2 text-center text-uppercase mt-3 mt-md-5"><b>Suspect's bio information</b>
    </h3>
    <hr class="m-0 pt-0">
    <div class="row pt-2">
        <div class="col-md-6 pl-5 pl-md-5">
            @include('components.detail-item', ['t' => 'Phone number', 's' => $s->phone_number])
            @include('components.detail-item', [
                't' => 'National ID number',
                's' => $s->national_id_number,
            ])

            @include('components.detail-item', ['t' => 'Parish', 's' => $s->parish])
            @include('components.detail-item', ['t' => 'Village', 's' => $s->village])

        </div>
        <div class="col-md-6 border-left pl-2 pl-5">
            @if ($s->is_offence_committed_in_pa)
                @include('components.detail-item', ['t' => 'Is offence committed in pa?', 's' => 'Yes'])
            @else
                @include('components.detail-item', ['t' => 'Is offence committed in pa?', 's' => 'No'])
            @endif

            @if ($s->pa != null)
                @include('components.detail-item', ['t' => 'PA', 's' => $s->pa->nme])
            @else
                @include('components.detail-item', ['t' => 'PA', 's' => '-'])
            @endif

            @include('components.detail-item', ['t' => 'GPS Latitude', 's' => $s->latitude])
            @include('components.detail-item', ['t' => 'GPS Longitude', 's' => $s->longitude])
        </div>
    </div>


    {{-- <h3 class="text-uppercase h3 p-0 m-0 mb-2 text-center  mt-3 mt-md-5">This is a simple case title</h3>
    <hr class="m-0 pt-0">
    <div class="row pt-2">
        <div class="col-md-6 pl-5 pl-md-5">
            @include('components.detail-item', ['t' => 'title', 's' => 'Detail'])
            @include('components.detail-item', ['t' => 'title', 's' => 'Detail'])
            @include('components.detail-item', ['t' => 'title', 's' => 'Detail'])
            @include('components.detail-item', ['t' => 'title', 's' => 'Detail'])
        </div>
        <div class="col-md-6 border-left pl-2 pl-5">
            @include('components.detail-item', ['t' => 'title', 's' => 'Detail'])
            @include('components.detail-item', ['t' => 'title', 's' => 'Detail'])
            @include('components.detail-item', ['t' => 'title', 's' => 'Detail'])
            @include('components.detail-item', ['t' => 'title', 's' => 'Detail'])
        </div>
    </div> --}}

    {{-- 	
created_at	
updated_at	
case_id	
exhibit_catgory	
wildlife	
implements	
photos	
description	
quantity	
    --}}
    <hr class="my-5">
    <h3 class="text-uppercase h3 p-0 m-0 mb-2 text-center  mt-3 mt-md-5"><b>Case Exhibits</b></h3>
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
                    @foreach ([] as $e)
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
    <h3 class="text-uppercase h3 p-0 m-0 mb-2 text-center  mt-3 mt-md-5"><b>Case Suspects</b></h3>
    <div class="row">
        <div class="col-12">
            <table class="table table-striped table-hover">
                <thead class="bg-primary">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Sex</th>
                        <th scope="col">Date of birth</th>
                        <th scope="col">Arrested</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ([] as $s)
                        <tr>
                            <th width="5%" scope="row">#{{ $s->id ?? '-' }}</th>
                            <td width="10%"><img class="border img-fluid rounded p-1" class="img-fluid"
                                    src="{{ url('assets/user.jpeg') ?? '-' }}"></td>
                            <td>{{ $s->sex ?? '-' }} KGs</td>
                            <td>{{ $s->age ?? '-' }}</td>
                            <td>{{ $s->is_suspects_arrested ? 'Arrested' : 'Not Arrested' ?? '-' }}</td>
                            <td width="20%">
                                <a class="text-primary" href="{{ admin_url() ?? '-' }}">See full details about this
                                    suspect</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>


    <hr class="my-5">
    <h3 class="text-uppercase h3 p-0 m-0 mb-2 text-center  mt-3 mt-md-5"><b>Case Progress Commenrs</b></h3>
    <div class="row">
        <div class="col-12">
            <table class="table table-striped table-hover">
                <thead class="bg-primary">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Sex</th>
                        <th scope="col">Date of birth</th>
                        <th scope="col">Arrested</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ([] as $s)
                        <tr>
                            <th width="5%" scope="row">#{{ $s->id ?? '-' }}</th>
                            <td width="10%"><img class="border img-fluid rounded p-1" class="img-fluid"
                                    src="{{ url('assets/user.jpeg') ?? '-' }}"></td>
                            <td>{{ $s->sex ?? '-' }} KGs</td>
                            <td>{{ $s->age ?? '-' }}</td>
                            <td>{{ $s->is_suspects_arrested ? 'Arrested' : 'Not Arrested' ?? '-' }}</td>
                            <td width="20%">
                                <a class="text-primary" href="{{ admin_url() ?? '-' }}">See full details about this
                                    suspect</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>



    {{-- <hr class="mb-4 mt-0  border-primary pt-0 mb-md-5"> --}}

</div>
<style>
    .content-header {
        display: none;
    }
</style>
