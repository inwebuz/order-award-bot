@extends('layouts.app')

@section('title', __('Category') . '#' . $category)
@section('body_class', 'payment-page')

@section('content')
    <div class="container">

    	<h1>
    		{{ __('Category') }} #{{ $category }}
    	</h1>
        
        <div class="py-4 d-print-none">
            <a href="/categories" class="btn btn-info">{{ __('Back') }}</a>
        </div>
    	
    </div>
@endsection
