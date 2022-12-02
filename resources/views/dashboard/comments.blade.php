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

    .case-item:hover {
        background-color: rgb(254, 254, 254);
    }
</style>
<div class="card  mb-4 mb-md-5 border-0">
    <!--begin::Header-->
    <div class="d-flex justify-content-between px-3 px-md-4 ">
        <h3>
            <b>Cases of interest progress</b>
        </h3> 
    </div>
    <div class="card-body py-2 py-md-3">
        @foreach ($items as $i)
            <div class="d-flex align-items-center mb-4 case-item">
                <div style="border-left: solid #277C61 5px;" class="flex-grow-1 pl-2 pl-md-3 ">
                    <a href="{{ url("/case-suspects/{$i->suspect_id}") }}" class="text-dark text-hover-primary">
                        <b>{{ Str::of($i->body)->limit(40) }}</b>
                    </a>
                    <span class="text-muted fw-bold d-block">{{ Utils::my_time_ago($i->created_at) }}
                        - <b>{{ $i->reporter->name }}</b>
                    </span>
                </div>
                <a href="{{ url("/case-suspects/{$i->suspect_id}") }}" class="badge "
                    style="background-color: #277C61;"> <i class="fa fa-chevron-right"></i> </a>
            </div>
        @endforeach
    </div>
</div>
