<?php
$i = 0;
?><div style="font-size: 1.8rem">
    @foreach ($case->exhibits as $sus)
        <?php $i++; ?>
        <div class="">
            Exhibit  <b>{{ $i }}.</b>
            {{--  {{ $sus->exhibit_catgory }} - {{ $sus->description }} , {{ $sus->quantity }}
            Exhibit --}}

            <small><u><a href="/new-exhibits-case-models/{{ $sus->id }}/edit"
                        title="Edit this suspect's information">Edit</a></u></small>
            <small><u>
                    <a href="javascript:;" {{-- href="/new-exhibits-case-models/{{ $case->id }}/edit?remove_suspect={{ $sus->id }}" --}} class="text-danger"
                        title="Remove this suspect from this case">Remove</a></u>
            </small>

        </div>
    @endforeach

    <div>
        <b>ACTION:</b> <small><a href="{{ admin_url('new-exhibits-case-models/create') }}"
                title="Add, Edit or Remove exhibit" class="text-success"><u>Add exhibit</u></a></small>
    </div>
</div>
