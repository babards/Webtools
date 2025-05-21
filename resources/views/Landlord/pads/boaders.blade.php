@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Boarders for {{ $pad->padName }}</h2>

    @if($boarders->count())
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Tenant Name</th>
                    <th>Application Date</th>
                    <th>Contact</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                @foreach($boarders as $boarder)
                    <tr>
                        <td>{{ $boarder->tenant->first_name ?? 'N/A' }} {{ $boarder->tenant->last_name ?? '' }}</td>
                        <td>{{ $boarder->application_date->format('Y-m-d') }}</td>
                        <td>{{ $boarder->tenant->contact ?? 'N/A' }}</td>
                        <td>{{ $boarder->tenant->email ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <a href="{{ route('landlord.pads.index') }}" class="btn btn-secondary mb-3">Back to Pads</a>
    @else
        <p>No approved boarders for this pad yet.</p>
    @endif
</div>
@endsection
