<?php
use App\Models\Utils;
?><style>
    .my-table th {
        border: 4px solid black !important;
    }
    .my-table td,.my-table tr {
        border: 4px solid black !important;
    }
</style>
<div class="container bg-white p-1 p-md-5">
    <div class="d-md-flex justify-content-between">
        <div class="">
            <h2 class="m-0 p-0 mb-3 text-primary h3"><b>Exhibit details</b>
            </h2>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <table class="table my-table table-bordered table-striped table-hover" style="border: 3px solid black!important;">
                <thead class="bg-primary" style="border: 3px solid black!important;">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Photos</th>
                        <th scope="col">Category</th>
                        <th scope="col">Quantity (KGs) & No. of Pieces</th>
                        <th scope="col">Description</th>
                        {{--                         <th scope="col">Action</th> --}}
                    </tr>
                </thead>
                <tbody>
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
                                's' => $e->wildlife_quantity ? $e->wildlife_quantity . ' KGs' : '-',
                            ])
                            @include('components.detail-item', [
                                't' => 'Wildlife',
                                's' => $e->wildlife_pieces,
                            ])
                            @include('components.detail-item', [
                                't' => 'IMPLEMENTs',
                                's' => $e->implement_pieces,
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
                </tbody>
            </table>

        </div>
    </div>
</div>
