<?php
$i = 0;
?><div style="font-size: 1.8rem">@foreach ($case->exhibits as $sus)
    <?php $i++; ?>

    <div class="">
        <b>{{ $i }}.</b> {{ $sus->exhibit_catgory }} - {{ $sus->description }} , {{ $sus->quantity }}
        KGs/Pieces.

    </div>
@endforeach

    <div>
        <b>ACTION:</b> <small><a href="{{ admin_url("new-exhibits-case-models/{$case->id}/edit") }}" title="Add, Edit or Remove exhibit"
                class="text-success"><u>Modify</u></a></small>
    </div>
</div>
