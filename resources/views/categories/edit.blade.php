@extends('layouts.app')

@section('title', __('Edit category'))

@section('content')
    <div class="container">

        <h1>{{ __('Edit category') }}</h1>

        <div class="py-4">
            <a href="{{ route('categories.index') }}" class="btn btn-info">{{ __('Back') }}</a>
        </div>

        <form action="{{ route('categories.update', $category->id) }}" method="POST">

            @csrf
            @method('PUT')

            @include('categories.partials.form')

            <div class="form-group">
                <button class="btn btn-lg btn-success" type="submit">{{ __('Send') }}</button>
            </div>
        </form>

    </div>
@endsection
