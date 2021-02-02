@extends('layouts.app')

@section('title', __('Orders upload'))
@section('body_class', 'payment-page')

@section('content')
    <div class="container">

        <h1>
            {{ __('Orders upload') }}
        </h1>

        <div class="my-4">
            <a href="/Upload-Orders-Template.xlsx" download>{{ __('Download template') }}</a>
        </div>

        <form action="{{ route('orders.upload.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label>{{ __('Choose file') }}</label>
                <input type="file" name="upload" class="form-control">
                @error('upload')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <button class="btn btn-primary">{{ __('Upload') }}</button>
            </div>
        </form>

    </div>
@endsection
