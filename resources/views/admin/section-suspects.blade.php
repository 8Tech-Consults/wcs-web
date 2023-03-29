<?php
if (!isset($items)) {
    $items = [];
}
?><div class="row">
    <div class="col-12">
        <table class="table table-striped table-hover">
            <thead class="bg-primary">
                <tr>
                    <th scope="col">Photo</th>
                    <th scope="col">Suspect number</th>
                    <th scope="col">Name</th>
                    <th scope="col">Sex</th>
                    <th scope="col">Is Arrested</th>
                    <th scope="col">In Court</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $sus)
                    <tr>

                        <td width="10%">
                            @if ($sus->photo_url != null && strlen($sus->photo_url) > 5)
                                <img class="border img-fluid rounded p-1" class="img-fluid" src="{{ $sus->photo_url }}">
                            @else
                                <b>No Photo</b>
                            @endif

                        </td>
                        <th width="5%" scope="row">#{{ $sus->uwa_suspect_number ?? '-' }}</th>
                        <td>{{ $sus->name ?? '-' }}</td>
                        <td>{{ $sus->sex ?? '-' }}</td>
                        <td>{{ $sus->is_suspects_arrested ?? 'No' }}</td>
                        <td>{{ $sus->is_suspect_appear_in_court ?? 'No' }}</td>

                        <td width="20%">
                            <a class="text-primary" href="{{ admin_url('case-suspects/' . $sus->id) ?? '-' }}">View
                                Details</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>
