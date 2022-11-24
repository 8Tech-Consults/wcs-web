<?php
use App\Models\Utils;
?><div class="container bg-white p-1 p-md-5">
    <div class="d-md-flex justify-content-between">
        <div class="">
            <h2 class="m-0 p-0 text-dark h3"><b>Offence {{ '#' . $c->id }} - details</b>
            </h2>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ url('cases') }}" class="btn btn-secondary btn-sm"><i class="fa fa-chevron-left"></i> BACK
                TO ALL OFFENCES</a>
            <a href="{{ url('cases/' . $c->id . '/edit') }}" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i>
                EDIT</a>
            <a href="#" onclick="window.print();return false;" class="btn btn-primary btn-sm"><i
                    class="fa fa-print"></i> PRINT</a>
        </div>
    </div>
    <hr class="my-3 my-md-4">
    <div class="row">
        <div class="col-3 col-md-2">
            <div class="border border-1 rounded bg-">
                <img class="img-fluid" src="{{ url('assets/user.jpeg') }}">
            </div>
        </div>
        <div class="col-9 col-md-6">
            <h3 class="h3 p-0 m-0">{{ $c->title }}</h3>
            <hr class="my-1 my-md-4">
            @include('components.detail-item', ['t' => 'OFFENCE ID', 's' => '#' . $c->id])
            @include('components.detail-item', [
                't' => 'Report date',
                's' => Utils::my_date_time($c->created_at),
            ])
            @include('components.detail-item', ['t' => 'Reported by', 's' => $c->reportor->name])
            @include('components.detail-item', ['t' => 'Offence category', 's' => $c->offence_category_id])
        </div>
        <div class="pt-3 pt-md-0 col-md-4">
            <div class=" border border-primary p-3">
                <h2 class="m-0 p-0 text-dark h3 text-center"><b>Offence Summary</b></h2>
                <hr class="border-primary mt-3">
                <div style="font-family: monospace; font-size: 16px;">
                    <p class="py-1 my-0"><b>STATUS:</b>

                        <span class="badge badge-{{ Utils::tell_case_status_color($c->status) }}">
                            {{ Utils::tell_case_status($c->status) }}
                        </span>
                    </p>
                    <p class="py-1 my-0 text-uppercase"><b>Number of Exhibits:</b> {{ count($c->exhibits) }}</p>
                    <p class="py-1 my-0 text-uppercase"><b>Number of Suspects:</b> {{ count($c->suspects) }}</p>
                </div>
            </div>
        </div>
    </div>

    <hr class="mt-4 mb-2 border-primary pb-0 mt-md-5 mb-md-5">
    <h3 class="h3 p-0 m-0 mb-2 text-center mt-3 mt-md-5"><b>Offence location details</b></h3>
    <hr class="m-0 pt-0">
    <div class="row pt-2">
        <div class="col-md-6 pl-5 pl-md-5">
            @include('components.detail-item', ['t' => 'Dsitrict', 's' => $c->district->name])
            @include('components.detail-item', ['t' => 'Subcount', 's' => $c->sub_county->name])
            @include('components.detail-item', ['t' => 'Parish', 's' => $c->parish])
            @include('components.detail-item', ['t' => 'Village', 's' => $c->village])

        </div>
        <div class="col-md-6 border-left pl-2 pl-5">
            @if ($c->is_offence_committed_in_pa)
                @include('components.detail-item', ['t' => 'Is offence committed in pa?', 's' => 'Yes'])
            @else
                @include('components.detail-item', ['t' => 'Is offence committed in pa?', 's' => 'No'])
            @endif

            @if ($c->pa != null)
                @include('components.detail-item', ['t' => 'PA', 's' => $c->pa->nme])
            @else
                @include('components.detail-item', ['t' => 'PA', 's' => '-'])
            @endif

            @include('components.detail-item', ['t' => 'GPS Latitude', 's' => $c->latitude])
            @include('components.detail-item', ['t' => 'GPS Longitude', 's' => $c->longitude])
        </div>
    </div>


    <hr class="my-5">
    <h3 class="h3 p-0 m-0 mb-2 text-center  mt-3 mt-md-5"><b>Offence Exhibits</b></h3>
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
                    @foreach ($c->exhibits as $e)
                        <tr>
                            <th width="5%" scope="row">#{{ $e->id }}</th>
                            <td width="10%"><img class="border img-fluid rounded p-1" class="img-fluid"
                                    src="{{ url('assets/user.jpeg') }}"></td>
                            <td>{{ number_format((int) $e->id) }} KGs</td>
                            <td>{{ $e->exhibit_catgory }}</td>
                            <td>{{ $e->description }}</td>
                            <td width="20%">
                                <a class="text-primary" href="{{ admin_url() }}">See full details about this
                                    exhibit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>


    <hr class="my-5">
    <h3 class="h3 p-0 m-0 mb-2 text-center  mt-3 mt-md-5"><b>Offence Suspects</b></h3>

    @include('admin/section-suspects', ['items' => $c->suspects])


    <hr class="my-5">
    <h3 class="h3 p-0 m-0 mb-2 text-center  mt-3 mt-md-5"><b>Offence Progress Comments</b></h3>
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
                    @foreach ($c->suspects as $s)
                        <tr>
                            <th width="5%" scope="row">#{{ $s->id }}</th>
                            <td width="10%"><img class="border img-fluid rounded p-1" class="img-fluid"
                                    src="{{ url('assets/user.jpeg') }}"></td>
                            <td>{{ $s->sex }} KGs</td>
                            <td>{{ $s->age }}</td>
                            <td>{{ $s->is_suspects_arrested ? 'Arrested' : 'Not Arrested' }}</td>
                            <td width="20%">
                                <a class="text-primary" href="{{ admin_url() }}">See full details about this
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
