@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">System Logs</h4>
                </div>
                <div class="card-body">
                    <!-- Search and Filters -->
                    <form action="{{ route('admin.logs.index') }}" method="GET" class="row g-2 mb-3 align-items-center">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="action_filter" class="form-select" onchange="this.form.submit()">
                                <option value="">All Actions</option>
                                @foreach($actions as $action)
                                    <option value="{{ $action }}" {{ request('action_filter') == $action ? 'selected' : '' }}>
                                        {{ ucfirst($action) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="user_filter" class="form-select" onchange="this.form.submit()">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_filter') == $user->id ? 'selected' : '' }}>
                                        {{ $user->first_name }} {{ $user->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_from" class="form-control" placeholder="From Date" value="{{ request('date_from') }}" onchange="this.form.submit()">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_to" class="form-control" placeholder="To Date" value="{{ request('date_to') }}" onchange="this.form.submit()">
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-2">Date & Time</th>
                                    <th class="py-2">User</th>
                                    <th class="py-2">Action</th>
                                    <th class="py-2">Description</th>
                                    <th class="py-2">IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td class="py-2">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                        <td class="py-2">
                                            @if($log->user)
                                                {{ $log->user->first_name }} {{ $log->user->last_name }}
                                            @else
                                                <span class="text-muted">Guest</span>
                                            @endif
                                        </td>
                                        <td class="py-2">{{ ucfirst($log->action) }}</td>
                                        <td class="py-2">{{ $log->description }}</td>
                                        <td class="py-2">{{ $log->ip_address }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">No logs found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $logs->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 