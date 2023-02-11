<?php

if (!isset($active)) {
    $active = 1;
}

$suspects_count = 0;
$exhibits_count = 0;
if ( isset($case) && $case != null) {
    $suspects_count = count($case->suspects);
    $exhibits_count = count($case->exhibits);
}

$tab_2 = '  ';
$tab_3 = '  ';
$tab_4 = '  ';
if ($active == 2) {
    $tab_2 = ' active-step ';
} elseif ($active == 3) {
    $tab_2 = ' active-step ';
    $tab_3 = ' active-step ';
} elseif ($active == 4) {
    $tab_2 = ' active-step ';
    $tab_3 = ' active-step ';
    $tab_4 = ' active-step ';
}

?><div class="row my-steppers">
    <div class="col-md-3  active-step">
        1. Case
    </div>
    <div class="col-md-3 {{ $tab_2 }}">
        <p>2. Suspects {{ $suspects_count<1 ? "" : "($suspects_count)"}}</p>
    </div>
    <div class="col-md-3 {{ $tab_3 }}">
        <p>3. Exhibits {{ $exhibits_count<1 ? "" : "($exhibits_count)"}} </p> 
    </div>
    <div class="col-md-3 {{ $tab_4 }}">
        <p>4. Submit</p>
    </div>
</div>
{{-- 
    
    http://127.0.0.1:8000/new-case-suspects/create
    --}}