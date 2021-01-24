@extends('layouts.app')

@section('title', __('Review') . '#' . $review)
@section('body_class', '')

@section('content')
    <div class="container">

    	<h1>
    		{{ __('Review') }} #{{ $review }}
    	</h1>
        
        <div class="py-4 d-print-none">
            <a href="/reviews" class="btn btn-info">{{ __('Back') }}</a>
        </div>
    	
    </div>
@endsection
