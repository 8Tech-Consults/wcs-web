<?php
use App\Models\Utils;
?><style>
    .ext-icon {
        color: rgba(0, 0, 0, 0.5);
        margin-left: 10px;
    }

    .installed {
        color: #00a65a;
        margin-right: 10px;
    }

    .card {
        border-radius: 5px;
    }
</style>
<div class="card  mb-4 mb-md-5 border-0">
    <!--begin::Header-->
    <div class="d-flex justify-content-between px-3 px-md-4 ">
        <h3>
            <b>Recent suspects</b>
        </h3>
        <div>
            <a href="{{ url('/case-suspects') }}" class="btn btn-sm btn-primary mt-md-4 mt-4">
                View All
            </a>
        </div>
    </div>
    <div class="card-body py-0">
        <!--begin::Table container-->
        <div class="table-responsive">
            <!--begin::Table-->
            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                <!--begin::Table head-->
                <thead>
                    <tr class="fw-bolder text-muted">
                        <th class="min-w-200px">Suspect</th>
                        <th class="min-w-150px">Case Title</th>
                        <th class="min-w-150px">Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($suspects as $suspect)
                        <tr>

                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="symbol"
                                        style="width: 55px; height: 60px;
                                    background-image: url({{ $suspect->photo_url }});
                                    background-position: center;
                                    background-size: cover;
                                    border-radius: 8px;
                                    ">
                                    </div>
                                    <div class="d-flex justify-content-start flex-column pl-3">
                                        <a href="#" style="color: black; font-weight: 600;"
                                            class="text-dark fw-bolder text-hover-primary fs-6">{{ $suspect->name }}</a>
                                        <span
                                            class="text-muted fw-bold text-muted d-block fs-7">{{ $suspect->sex }}</span>
                                        <span class="text-muted fw-bold text-muted d-block fs-7">
                                            <b class="p-0 m-0 small text-dark">COUNTRY:</b>
                                            {{ Str::of($suspect->country)->limit(10) }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <b class="text-dark fw-bold  d-block fs-7"
                                    style="color: black">{{ Str::of($suspect->case->title)->limit(35) }}</b>
                                <span class="fw-bold text-primary d-block fs-7">{{ $suspect->case->updated_at }}</span>
                            </td>
                            <td class="text-end">
                                <span class="badge bg-{{ Utils::tell_suspect_status_color($suspect) }}">
                                    {{ Utils::tell_suspect_status($suspect) ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <div class=" justify-content-end text-right ">
                                    <a href="{{ url("/case-suspects/{$suspect->id}") }}" title="View"
                                        class="btn btn-icon btn-bg-light  text-dark  me-1 p-0 px-2 m-0"
                                        style="font-size: 16px;">

                                        <i class="fa fa-eye"></i>

                                        <span>View</span>
                                        <!--end::Svg Icon-->
                                    </a><br>
                                    <a href="{{ url("/case-suspects/{$suspect->id}") }}/edit" title="View"
                                        class="btn btn-icon btn-bg-light text-primary   me-1 p-0 px-2 m-0"
                                        style="font-size: 16px;">

                                        <i class="fa fa-edit"></i>

                                        <span class="ml-2">Edit</span>
                                    </a>


                                </div>
                            </td>
                        </tr>
                    @endforeach

                </tbody>
                <!--end::Table body-->
            </table>
            <!--end::Table-->
        </div>
        <!--end::Table container-->
    </div>
    <!--begin::Body-->
</div>
