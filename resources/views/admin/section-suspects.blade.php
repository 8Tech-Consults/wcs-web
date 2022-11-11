<?php
if (!isset($items)) {
    $items = [];
}
?><div class="row">
    <div class="col-12">
        <table class="table table-striped table-hover">
            <thead class="bg-primary">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Photo</th>
                    <th scope="col">Name</th>
                    <th scope="col">Sex</th>
                    <th scope="col">Date of birth</th>
                    <th scope="col">Arrested</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $sus)
                    <tr>
                        <th width="5%" scope="row">#{{ $sus->id ?? '-' }}</th>
                        <td width="10%"><img class="border img-fluid rounded p-1" class="img-fluid"
                                src="{{ $sus->photo_url }}"></td>

                        <td>{{ $sus->name ?? '-' }}</td>
                        <td>{{ $sus->sex ?? '-' }}</td>
                        <td>{{ $sus->age ?? '-' }}</td>
                        <td>{{ $sus->is_suspects_arrested ? 'Arrested' : 'Not Arrested' ?? '-' }}</td>

                        <td width="20%"> 
                            <a class="text-primary" href="{{ admin_url('case-suspects/' . $sus->id) ?? '-' }}">See full
                                details about this
                                suspect</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>
