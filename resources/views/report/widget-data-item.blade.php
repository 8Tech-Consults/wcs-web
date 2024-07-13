<?php
$number = $number ?? 0;
$number = (is_numeric($number)) ? $number : 0; 
?><div style="display: inline-block; width: 98%;">
    <div class="border border-success rounded-5 text-center p-2 mb-3 text-center "
        style="
        border-top-right-radius: 30px; 
        border-bottom-left-radius: 30px;
        border-width: 8px!important; 
        height: 150px;
        ">
        <p class="fs-50"> {{ number_format($number) }} </p>
        <p class="fs-14" style="line-height: 14px;">{{ $title }}</p>
    </div>
</div>
