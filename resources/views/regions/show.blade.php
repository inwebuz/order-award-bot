@extends('layouts.app')

@section('title', __('Region') . '#' . $region)
@section('body_class', 'payment-page')

@section('content')
    <div class="container">

    	<h1>
    		{{ __('Region') }} #{{ $region }}
    	</h1>
        
        <div class="py-4 d-print-none">
            <a href="/regions" class="btn btn-info">{{ __('Back') }}</a>
        </div>
    	
    </div>
@endsection
