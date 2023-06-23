<?php
use App\Models\Utils;
?>
<tr>
    <th width="5%" scope="row" rowspan="3">#{{ $e->id }}</th>
    <td width="25%" rowspan="3">
        <?php
        $pics = $e->get_photos();
        ?>
        @if (count($pics) < 1)
            <b>No photo.</b>
        @else
            @foreach ($pics as $pic)
                <a href="{{ $pic }}" target="_blank" title="Click to view full image" rel="noopener noreferrer">
                    <img class="border img-fluid rounded p-1" width="45%" class="img-fluid"
                        src="{{ $pic }}"></a>
            @endforeach
        @endif

    </td>
    <td>
        @include('components.detail-item', [
            't' => 'Wildlife Species ',
            's' => $e->get_species(),
        ])

        @include('components.detail-item', [
            't' => 'Specimen',
            's' => $e->specimen,
        ])
    </td>
    <td>
        @include('components.detail-item', [
            't' => 'Quantity',
            's' => $e->wildlife_quantity ? $e->wildlife_quantity . ' KGs' : '-',
        ])
        @include('components.detail-item', [
            't' => 'Pieces',
            's' => $e->wildlife_pieces ? $e->wildlife_pieces : '-',
        ])
    </td>

    <td>
        @include('components.detail-item', [
            't' => 'Wildlife?',
            's' => $e->wildlife_description,
        ])
    </td>

    {{-- <td width="20%">
                            <a class="text-primary" href="{{ admin_url() }}">See full details about this
                                exhibit</a>
                        </td> --}}
</tr>
<tr>
    <td>
        @include('components.detail-item', [
            't' => 'IMPLEMENT ',
            's' => $e->get_implement(),
        ])
    </td>

    <td>
        @include('components.detail-item', [
            't' => 'PIECES',
            's' => $e->implement_pieces,
        ])
    </td>

    <td>
        @include('components.detail-item', [
            't' => 'IMPLEMENT?',
            's' => $e->implement_description,
        ])
    </td>
</tr>
<tr>
    <td>
        @include('components.detail-item', [
            't' => 'HAS OTHERS?',
            's' => $e->type_other,
        ])
    </td>

    <td>
        @include('components.detail-item', [
            't' => 'OTHERS',
            's' => 'N/A',
        ])
    </td>

    <td>
        @include('components.detail-item', [
            't' => 'OTHERS?',
            's' => $e->others_description,
        ])
    </td>

    {{-- <td width="20%">
                            <a class="text-primary" href="{{ admin_url() }}">See full details about this
                                exhibit</a>
                        </td> --}}
</tr>
