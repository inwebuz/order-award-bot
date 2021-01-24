@extends('layouts.app')

@section('title', __('Product') . '#' . $product)
@section('body_class', 'payment-page')

@section('content')
    <div class="container">

        <h1>
            {{ __('Product') }} #{{ $product }}
        </h1>

        <div class="py-4 d-print-none">
            <a href="/products" class="btn btn-info">{{ __('Back') }}</a>
        </div>

    </div>
@endsection
