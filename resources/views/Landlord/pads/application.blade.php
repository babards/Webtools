@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Applications for {{ $pad->padName }}</h2>
    @if($applications->count())
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Tenant</th>
                    <th>Application Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($applications as $application)
                    <tr>
                        <td>{{ $application->tenant->first_name ?? 'N/A' }} {{ $application->tenant->last_name ?? '' }}</td>
                        <td>{{ $application->application_date->format('Y-m-d') }}</td>
                        <td>
                            <span class="badge 
                                @if($application->status == 'approved') bg-success
                                @elseif($application->status == 'rejected') bg-danger
                                @else bg-warning text-dark
                                @endif
                            ">
                                {{ ucfirst($application->status) }}
                            </span>
                        </td>
                        <td>
                            @if($application->status == 'pending')
                                <form action="{{ route('landlord.applications.approve', ['applicationId' => $application->id]) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                </form>
                                <form action="{{ route('landlord.applications.reject', ['applicationId' => $application->id]) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            @else
                                <span class="text-muted">No actions</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <a href="{{ route('landlord.pads.index') }}" class="btn btn-secondary mb-3">Back to Pads</a>
        {{ $applications->links() }}
    @else
        <p>No applications for this pad.</p>
    @endif
</div>
@endsection
