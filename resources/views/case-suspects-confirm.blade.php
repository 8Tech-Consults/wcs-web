<div style="font-size: 1.8rem">
    @foreach ($case->suspects as $sus)
        <div class="">
            <b>{{ $loop->iteration }}.</b> {{ $sus->suspect_number }} - {{ $sus->name }}.

            <small><u><a href="/new-case-suspects/{{ $sus->id }}/edit"
                        title="Edit this suspect's information">Edit</a></u></small>

            @if ($case->suspects()->count() > 1)
                <small><u><a href="/new-confirm-case-models/{{ $case->id }}/edit?remove_suspect={{ $sus->id }}"
                            class="text-danger" title="Remove this suspect from this case">Remove</a></u>
                </small>
            @endif
        </div>
    @endforeach

    <div>
        <b>ACTION:</b> <small><a href="/new-case-suspects/create" title="Add another suspect to this case"
                class="text-success"><u>Add suspect</u></a></small>
    </div>
</div>
