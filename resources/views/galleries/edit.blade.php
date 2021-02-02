@extends('layouts.app')

@section('title', __('Edit'))

@section('content')
    <div class="container">

        <h1>{{ __('Edit') }}</h1>

        <div class="py-4">
            <a href="{{ route('galleries.index') }}" class="btn btn-info">{{ __('Back') }}</a>
        </div>

        <form action="{{ route('galleries.update', $gallery->id) }}" method="POST" enctype="multipart/form-data">

            @csrf
            @method('PUT')

            @include('galleries.partials.form')

            <div class="form-group">
                <button class="btn btn-lg btn-success" type="submit">{{ __('Send') }}</button>
            </div>
        </form>

    </div>
@endsection
