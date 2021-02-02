@extends('layouts.app')

@section('title', __('Add'))

@section('content')
    <div class="container">

        <h1>{{ __('Add') }}</h1>

        <div class="py-4">
            <a href="{{ route('galleries.index') }}" class="btn btn-info">{{ __('Back') }}</a>
        </div>

        <form action="{{ route('galleries.store') }}" method="POST" enctype="multipart/form-data">

            @csrf

            @include('galleries.partials.form')

            <div class="form-group">
                <button class="btn btn-lg btn-success" type="submit">{{ __('Send') }}</button>
            </div>
        </form>

    </div>
@endsection
