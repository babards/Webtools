@extends('layouts.app')

@section('content')
<div class="card shadow-sm" style="width: 100%; max-width: 400px;">
    <div class="card-body p-4">
        <h4 class="text-center mb-4">{{ __('Two Factor Authentication') }}</h4>

        @if (session('message'))
            <div class="alert alert-info" role="alert">
                {{ session('message') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('2fa.verify') }}">
            @csrf

            <div class="mb-3">
                <label for="two_factor_code" class="form-label">{{ __('2FA Code') }}</label>
                <input id="two_factor_code" type="text" 
                    class="form-control @error('two_factor_code') is-invalid @enderror" 
                    name="two_factor_code" 
                    required 
                    autocomplete="off" 
                    autofocus>

                @error('two_factor_code')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-primary">
                    {{ __('Verify Code') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 