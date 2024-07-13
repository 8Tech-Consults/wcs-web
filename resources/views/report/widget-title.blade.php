<?php
$start_date = Carbon\Carbon::parse($report->start_date);
$end_date = Carbon\Carbon::parse($report->end_date);
$date_generated = Carbon\Carbon::parse($report->date_generated);
?><div style="display: flex;">
    <div
        style="display: inline-block; 
    height: 80px;
    background-color: rgb(54, 51, 51);
    width: 45px; 
    color: white;
    ">
        <?= $number ?>
    </div>
    <div style="display: inline-block; 
    height: 80px;
    background-color: rgb(12, 131, 12);
    width: 5px; 
    ">
    </div>
    <div style="margin-left: 0px; display: inline-block; ">
        <span class="p-0 m-0"
            style=" 
            letter-spacing: 0.02px;
            line-height: 52px; 
            font-size: 50px;"><?= $title ?></span>
        <p class="p-0 m-0 mt-2" style="line-height: 16px;">FOR THE PERIOD OF
            <b><u>{{ $start_date->format('d M, Y') }} - {{ $end_date->format('d M, Y') }}</u></b> AS ON
            <b><u>{{ $date_generated->format('d M, Y') }}</u></b>.
        </p>
    </div>
</div>
