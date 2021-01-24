@extends('layouts.app')

@section('title', __('Edit region'))

@section('content')
    <div class="container">

        <h1>{{ __('Edit region') }}</h1>

        <div class="py-4">
            <a href="{{ route('regions.index') }}" class="btn btn-info">{{ __('Back') }}</a>
        </div>

        <form action="{{ route('regions.update', $region->id) }}" method="POST">

            @csrf
            @method('PUT')

            @include('regions.partials.form')

            <div class="form-group">
                <button class="btn btn-lg btn-success" type="submit">{{ __('Send') }}</button>
            </div>
        </form>

    </div>
@endsection
