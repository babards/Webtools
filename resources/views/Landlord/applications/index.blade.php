@extends('layouts.app')

@section('content')
<div class="container">
    <h2>All Tenant Applications</h2>
    @if($applications->count())
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Pad Name</th>
                    <th>Tenant</th>
                    <th>Application Date</th>
                    <th>Status</th>
                    <th>Message</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($applications as $application)
                    <tr>
                        <td>{{ $application->pad->padName ?? 'N/A' }}</td>
                        <td>{{ $application->tenant->first_name ?? 'N/A' }} {{ $application->tenant->last_name ?? '' }}</td>
                        <td>{{ $application->application_date->format('Y-m-d') }}</td>
                        <td>
                            <span class="badge 
                                @if($application->status == 'approved') bg-success
                                @elseif($application->status == 'rejected') bg-danger
                                @elseif($application->status == 'pending') bg-warning text-dark
                                @else bg-secondary
                                @endif
                            ">
                                {{ ucfirst($application->status) }}
                            </span>
                        </td>
                        <td style="max-width: 200px; word-break: break-word;">
                            {{ $application->message ?? 'No message' }}
                        </td>
                        <td>
                            @if($application->status == 'pending')
                                <form action="{{ route('landlord.applications.approve', $application->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                </form>
                                <form action="{{ route('landlord.applications.reject', $application->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            @else
                                <span class="text-muted">No action required</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $applications->links() }}
    @else
        <p>No applications found.</p>
    @endif
</div>
@endsection
