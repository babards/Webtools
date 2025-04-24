@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-body">
        <h4>Welcome, {{ Auth::user()->name }}</h4>
        <p class="text-muted">You are logged in to your account.</p>
    </div>
</div>
@endsection
