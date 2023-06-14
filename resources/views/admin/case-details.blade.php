<?php
use App\Models\Utils;
?><div class="container bg-white p-1 p-md-5">
    <div class="d-md-flex justify-content-between">
        <div class="">
            <h2 class="m-0 p-0 text-primary h3"><b>Case details</b>
            </h2>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ url('cases') }}" class="btn btn-secondary btn-sm"><i class="fa fa-chevron-left"></i> BACK
                TO ALL Case</a>

            {{--             @if (Auth::user()->isRole('admin'))
                <a href="{{ url('cases/' . $c->id . '/edit') }}" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i>
                    EDIT</a>
            @endif --}}
            <a href="#" onclick="window.print();return false;" class="btn btn-primary btn-sm"><i
                    class="fa fa-print"></i> PRINT</a>
        </div>
    </div>
    <hr class="my-3 my-md-4">
    <div class="row">

        <div class="col-9 col-md-8">
            <h3 class="h3 p-0 m-0">{{ $c->title }}</h3>
            <hr class="my-1 my-md-4">
            @include('components.detail-item', ['t' => 'Case NUMBER', 's' => '#' . $c->case_number])
            @include('components.detail-item', [
                't' => 'Report date',
                's' => Utils::my_date_time($c->created_at),
            ])
            @include('components.detail-item', ['t' => 'Complainant', 's' => $c->officer_in_charge])
            @include('components.detail-item', ['t' => 'Detection method', 's' => $c->detection_method])
            @include('components.detail-item', ['t' => 'Reported by', 's' => $c->reportor->name])

        </div>
        <div class="pt-3 pt-md-0 col-md-4">
            <div class=" border border-primary p-3">
                <h2 class="m-0 p-0 text-dark h3 text-center"><b>Case Summary</b></h2>
                <hr class="border-primary mt-3">
                <div style="font-family: monospace; font-size: 16px;">
                    <p class="py-1 my-0 text-uppercase"><b>Number of Exhibits:</b> {{ count($c->exhibits) }}</p>
                    <p class="py-1 my-0 text-uppercase"><b>Number of Suspects:</b> {{ count($c->suspects) }}</p>
                </div>
            </div>
        </div>
    </div>

    <hr class="mt-4 mb-2 border-primary pb-0 mt-md-5 mb-md-5">
    <h3 class="h3 p-0 m-0 mb-2 text-center mt-3 mt-md-5"><b>Case location details</b></h3>
    <hr class="m-0 pt-0">
    <div class="row pt-2">
        <div class="col-md-6 pl-5 pl-md-5">

            @if ($c->is_offence_committed_in_pa == 1 || $c->is_offence_committed_in_pa == 'Yes')
                @include('components.detail-item', ['t' => 'Is Case committed in pa?', 's' => 'Yes'])
            @else
                @include('components.detail-item', ['t' => 'Is Case committed in pa?', 's' => 'No'])
            @endif

            @if ($c->pa != null)
                @include('components.detail-item', ['t' => 'PA', 's' => $c->pa->name_text])
            @else
                @include('components.detail-item', ['t' => 'PA', 's' => '-'])
            @endif

            @include('components.detail-item', ['t' => 'GPS Latitude', 's' => $c->latitude])
            @include('components.detail-item', ['t' => 'GPS Longitude', 's' => $c->longitude])

        </div>
        <div class="col-md-6 border-left pl-2 pl-5">
            @if ($c->district != null)
                @include('components.detail-item', ['t' => 'Dsitrict', 's' => $c->district->name])
            @endif
            @if ($c->sub_county != null)
                @include('components.detail-item', ['t' => 'Subcount', 's' => $c->sub_county->name])
            @endif
            @include('components.detail-item', ['t' => 'Parish', 's' => $c->parish])
            @include('components.detail-item', ['t' => 'Village', 's' => $c->village])
        </div>
    </div>


    <hr class="my-5">
    <h3 class="h3 p-0 m-0 mb-2 text-center  mt-3 mt-md-5"><b>Case Exhibits</b></h3>
    <div class="row">
        <div class="col-12">
            <table class="table table-striped table-hover">
                <thead class="bg-primary">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Photos</th>
                        <th scope="col">Category</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">No. of Pieces</th>
                        <th scope="col">Description</th>
                        {{--                         <th scope="col">Action</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach ($c->exhibits as $e)
                        <?php
                        $pics = $e->get_photos();
                        ?>
                        <tr>
                            <th width="5%" scope="row">#{{ $e->id }}</th>
                            <td width="25%">
                                <?php
                                $pics = $e->get_photos();
                                ?>
                                @if (count($pics) < 1)
                                    <b>No photo.</b>
                                @else
                                    @foreach ($pics as $pic)
                                        <a href="{{ $pic }}" target="_blank" title="Click to view full image"
                                            rel="noopener noreferrer">
                                            <img class="border img-fluid rounded p-1" width="45%" class="img-fluid"
                                                src="{{ $pic }}"></a>
                                    @endforeach
                                @endif

                            </td>

                            <td>
                                @include('components.detail-item', [
                                    't' => 'Wildlife',
                                    's' => $e->wildlife_pieces,
                                ])



                                @include('components.detail-item', [
                                    't' => 'IMPLEMENT',
                                    's' => $e->implement_pieces,
                                ])
                                @include('components.detail-item', [
                                    't' => 'OTHERS',
                                    's' => 'N/A',
                                ])
                            </td>

                            <td>
                                @include('components.detail-item', [
                                    't' => 'Wildlife Species ',
                                    's' => $e->get_species(),
                                ])

                                @include('components.detail-item', [
                                    't' => 'Specimen',
                                    's' => $e->specimen,
                                ])

                                @include('components.detail-item', [
                                    't' => 'IMPLEMENT ',
                                    's' => $e->get_implement(),
                                ])
                                @include('components.detail-item', [
                                    't' => 'HAS OTHERS?',
                                    's' => $e->type_other,
                                ])
                            </td>


                            <td>
                                @include('components.detail-item', [
                                    't' => 'Wildlife',
                                    's' =>  ($e->wildlife_quantity) ? $e->wildlife_quantity." KGs" : "-"  ,
                                ])
                                @include('components.detail-item', [
                                    't' => 'IMPLEMENT',
                                    's' => 'N/A',
                                ])
                                @include('components.detail-item', [
                                    't' => 'OTHERS',
                                    's' => 'N/A',
                                ])
                            </td>
                            <td>
                                @include('components.detail-item', [
                                    't' => 'Wildlife?',
                                    's' => $e->wildlife_description,
                                ])
                                @include('components.detail-item', [
                                    't' => 'IMPLEMENT?',
                                    's' => $e->implement_description,
                                ])
                                @include('components.detail-item', [
                                    't' => 'OTHERS?',
                                    's' => $e->others_description,
                                ])
                            </td>

                            {{-- <td width="20%">
                                <a class="text-primary" href="{{ admin_url() }}">See full details about this
                                    exhibit</a>
                            </td> --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>


    <hr class="my-5">
    <h3 class="h3 p-0 m-0 mb-2 text-center  mt-3 mt-md-5"><b>Case Suspects</b></h3>

    @include('admin/section-suspects', ['items' => $c->suspects])


    <hr class="my-5">
    <h3 class="h3 p-0 m-0 mb-2 text-center  mt-3 mt-md-5"><b>Case Progress Comments</b></h3>
    <div class="row">
        <div class="col-12">
            <table class="table table-striped table-hover">
                <thead class="bg-primary">
                    <tr>
                        <th scope="col">Date</th>
                        <th scope="col">Comment by</th>
                        <th scope="col">Comment by</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($c->comments as $s)
                        <tr>
                            <th width="20%" scope="row">#{{ Utils::my_date_time($s->created_at) }}</th>
                            <td>{{ $s->body }}</td>
                            <td>{{ $s->reporter->name }}</td>
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
