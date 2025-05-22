@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Boarders for {{ $pad->padName }}</h2>

    @if($boarders->count())
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Tenant Name</th>
                    <th>Duration</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($boarders as $boarder)
                    <tr>
                        <td>{{ $boarder->tenant->first_name ?? 'N/A' }} {{ $boarder->tenant->last_name ?? '' }}</td>
                        @php
                            $start = \Carbon\Carbon::parse($boarder->created_at);
                            $now = \Carbon\Carbon::now();
                            $diff = $start->diff($now);
                        @endphp
                        <td>{{ $diff->m }} months and {{ $diff->d }} days</td>
                        <td>{{ $boarder->status ?? 'N/A' }}</td>
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
