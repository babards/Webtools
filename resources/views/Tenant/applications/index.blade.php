@extends('layouts.app')

@section('content')
<div class="container">
    <h2>My Applications</h2>
    <form method="GET" action="" class="row g-2 mb-3 align-items-end">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="pad_filter" class="form-select" onchange="this.form.submit()">
                <option value="">All Pads</option>
                @foreach($applications->pluck('pad.padName')->unique() as $padName)
                    <option value="{{ $padName }}" {{ request('pad_filter') == $padName ? 'selected' : '' }}>{{ $padName }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="status_filter" class="form-select" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="approved" {{ request('status_filter') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="pending" {{ request('status_filter') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="rejected" {{ request('status_filter') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="cancelled" {{ request('status_filter') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
        <div class="col-md-2">
            <a href="{{ route('tenant.applications.index') }}" class="btn btn-outline-secondary w-100">Reset Filters</a>
        </div>
    </form>
    
    @if($applications->count())
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Pad Name</th>
                    <th>Location</th>
                    <th>Application Date</th>
                    <th>Status</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>
                @foreach($applications as $application)
                    <tr>
                        <td>{{ $application->pad->padName ?? 'N/A' }}</td>
                        <td>{{ $application->pad->padLocation ?? 'N/A' }}</td>
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
                            @if($application->status == 'pending')
                                <form action="{{ route('tenant.applications.cancel', $application->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Are you sure you want to cancel this application?')" class="btn btn-danger btn-sm">Cancel</button>
                                </form>
                            @endif
                        </td>
                        <td>
                            {{ $application->message ?? 'No message' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $applications->links() }}
    @else
        <p>You have not applied for any pads yet.</p>
    @endif

    <a href="{{ route('tenant.pads.index') }}" class="btn btn-secondary mb-3">Back to Pads</a>
</div>
@endsection
