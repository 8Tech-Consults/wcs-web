<?php
use App\Models\Utils;
?><style>
    .my-table th {
        border: 4px solid black !important;
    }

    .my-table td,
    .my-table tr {
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
            <table class="table my-table table-bordered table-striped table-hover"
                style="border: 3px solid black!important;">
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
                    @include('components/exhibit-item', ['e' => $e])
                </tbody>
            </table>

        </div>
    </div>
</div>
