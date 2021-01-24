@extends('layouts.app')

@section('title', __('Edit product'))

@section('content')
    <div class="container">

        <h1>{{ __('Edit product') }}</h1>

        <div class="py-4">
            <a href="{{ route('products.index') }}" class="btn btn-info">{{ __('Back') }}</a>
        </div>

        <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">

            @csrf
            @method('PUT')

            @include('products.partials.form')

            <div class="form-group">
                <button class="btn btn-lg btn-success" type="submit">{{ __('Send') }}</button>
            </div>
        </form>

    </div>
@endsection
