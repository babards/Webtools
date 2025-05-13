@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Tenant Dashboard</h2>
    
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Browse Available Pads</h5>
                    <p class="card-text">Find your next home from our list of available pads!</p>
                    <a href="{{ route('pads.index') }}" class="btn btn-primary">Browse Pads</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
