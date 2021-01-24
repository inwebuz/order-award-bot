@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                        {{-- <a class="btn btn-info" href="{{ route('categories.index') }}">{{ __('Categories') }}</a> --}}
                        <a class="btn btn-lg btn-info" href="{{ route('products.index') }}">{{ __('Products') }}</a>
                        <a class="btn btn-lg btn-info" href="{{ route('orders.index') }}">{{ __('Orders') }}</a>
                        {{-- <a class="btn btn-info" href="{{ route('reviews.index') }}">{{ __('Reviews') }}</a> --}}

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
