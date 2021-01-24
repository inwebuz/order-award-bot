@extends('layouts.app')

@section('title', __('Edit review'))

@section('content')
    <div class="container">

        <h1>{{ __('Edit review') }}</h1>

        <div class="py-4">
            <a href="{{ route('reviews.index') }}" class="btn btn-info">{{ __('Back') }}</a>
        </div>

        <form action="{{ route('reviews.update', $review->id) }}" method="POST">

            @csrf
            @method('PUT')

            @include('reviews.partials.form')

            <div class="form-group">
                <button class="btn btn-lg btn-success" type="submit">{{ __('Send') }}</button>
            </div>
        </form>

    </div>
@endsection
