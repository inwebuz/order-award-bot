@extends('layouts.app')

@section('title', __('Edit order'))

@section('content')
    <div class="container">

        <h1>{{ __('Edit order') }}</h1>

        <div class="py-4">
            <a href="{{ route('orders.index') }}" class="btn btn-info">{{ __('Back') }}</a>
        </div>

        <form action="{{ route('orders.update', $order->id) }}" method="POST" enctype="multipart/form-data">

            @csrf
            @method('PUT')

            @include('orders.partials.form')

            <div class="form-group">
                <button class="btn btn-lg btn-success" type="submit">{{ __('Send') }}</button>
            </div>
        </form>

    </div>
@endsection
