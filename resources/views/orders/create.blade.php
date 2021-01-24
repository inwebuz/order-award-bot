@extends('layouts.app')

@section('title', __('Add product'))

@section('content')
    <div class="container">

        <h1>{{ __('Add product') }}</h1>

        <div class="py-4">
            <a href="{{ route('products.index') }}" class="btn btn-info">{{ __('Back') }}</a>
        </div>

        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">

            @csrf

            @include('products.partials.form')

            <div class="form-group">
                <button class="btn btn-lg btn-success" type="submit">{{ __('Send') }}</button>
            </div>
        </form>

    </div>
@endsection
