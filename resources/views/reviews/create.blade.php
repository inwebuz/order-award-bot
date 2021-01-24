@extends('layouts.app')

@section('title', __('Add review'))

@section('content')
    <div class="container">

        <h1>{{ __('Add review') }}</h1>

        <div class="py-4">
            <a href="{{ route('reviews.index') }}" class="btn btn-info">{{ __('Back') }}</a>
        </div>

        <form action="{{ route('reviews.store') }}" method="POST">

            @csrf

            @include('reviews.partials.form')

            <div class="form-group">
                <button class="btn btn-lg btn-success" type="submit">{{ __('Send') }}</button>
            </div>
        </form>

    </div>
@endsection
