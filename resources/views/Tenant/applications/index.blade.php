@extends('layouts.app')

@section('content')
<div class="container">
    <h2>My Applications</h2>
    
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
                            @if(in_array($application->status, ['pending', 'approved']))
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
