<div style="font-size: 1.8rem">
    <div class="">
        <b>CASE TITLE:</b> {{ $case->title }}
    </div>
    <div>
        <b>TEMPORARY CASE NUMBER:</b> {{ $case->case_number }}
    </div>
    <div>
        <b>CASE CATEGORY:</b> {{ $case->offense_category }}
    </div>
    <div>
        <b>CA:</b> {{ $case->ca->name }}
    </div>
    <div>
        <b>ACTION:</b> <small><a href="{{ admin_url("new-case/{$case->id}/edit") }}" title="Edit this case information" class="text-success"><u>Edit</u></a></small>
    </div>
</div>
