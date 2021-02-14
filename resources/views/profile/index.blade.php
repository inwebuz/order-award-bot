@extends('layouts.app')

@section('title', __('Profile'))
@section('body_class', 'payment-page')

@section('content')
    <div class="container">

        <h1>
            {{ __('Profile') }}
        </h1>

        @if (session()->has('success'))
            <div class="alert alert-success">{{ session()->get('success') }}</div>
        @endif

        <div class="my-4">
            <h4>{{ __('Change password') }}</h4>
            <div class="row">
                <div class="col-lg-4 col-sm-6">
                    <form action="{{ route('profile.update.password') }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="form_current_password">{{ __('Current password') }}</label>
                            <input type="password" name="current_password" id="form_current_password" class="form-control" required>
                            @error('current_password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="form_new_password">{{ __('New password') }}</label>
                            <input type="password" name="new_password" id="form_new_password" class="form-control" minlength="6" required>
                            @error('new_password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="form_new_password_confirmation">{{ __('Confirm password') }}</label>
                            <input type="password" name="new_password_confirmation" id="form_new_password_confirmation" class="form-control" minlength="6" required>
                        </div>

                        <button class="btn btn-primary" type="submit">{{ __('Send') }}</button>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection
