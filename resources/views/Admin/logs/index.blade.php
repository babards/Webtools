@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">System Logs</h3>
                </div>
                <div class="card-body">
                    <!-- Search and Filters -->
                    <form action="{{ route('admin.logs.index') }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <select name="action_filter" class="form-control" onchange="this.form.submit()">
                                        <option value="">All Actions</option>
                                        @foreach($actions as $action)
                                            <option value="{{ $action }}" {{ request('action_filter') == $action ? 'selected' : '' }}>
                                                {{ ucfirst($action) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <select name="user_filter" class="form-control" onchange="this.form.submit()">
                                        <option value="">All Users</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ request('user_filter') == $user->id ? 'selected' : '' }}>
                                                {{ $user->first_name }} {{ $user->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <input type="date" name="date_from" class="form-control" placeholder="From Date" value="{{ request('date_from') }}" onchange="this.form.submit()">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <input type="date" name="date_to" class="form-control" placeholder="To Date" value="{{ request('date_to') }}" onchange="this.form.submit()">
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Logs Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Description</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                        <td>
                                            @if($log->user)
                                                {{ $log->user->first_name }} {{ $log->user->last_name }}
                                            @else
                                                <span class="text-muted">Guest</span>
                                            @endif
                                        </td>
                                        <td>{{ ucfirst($log->action) }}</td>
                                        <td>{{ $log->description }}</td>
                                        <td>{{ $log->ip_address }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No logs found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $logs->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 