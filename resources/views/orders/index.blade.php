@extends('layouts.app')

@section('title', __('Products'))

@section('content')
    <div class="container">

        @if(session('success'))
            <div class="alert alert-success">
                {{ __(session('success')) }}
            </div>
        @endif

        <div class="my-4">
            <a href="{{ route('products.create') }}" class="btn btn-lg btn-info">
                {{ __('Add product') }}
            </a>
        </div>


        <h1>{{ __('Products') }}</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-light table-hover">
                <tr class="table-active">
                    <th>
                        {{ __('Title') }}
                    </th>
                    <th>
                        {{ __('Measurement unit') }}
                    </th>
                    <th>
                        {{ __('Price') }}
                    </th>
                    <th>
                        {{ __('Category') }}
                    </th>
                    <th></th>
                </tr>
                @forelse($products as $product)
                    <tr>
                        <td>{{ $product->title }}</td>
                        <td>{{ $product->description }}</td>
                        <td>{{ $product->price }}</td>
                        <td>{{ $product->category->title }}</td>
                        <td class="text-nowrap">
                            <a href="{{ route('products.edit', ['product' => $product->id]) }}" class="btn btn-sm btn-warning">{{ __('Edit') }}</a>
                            <a href="#" onclick="event.preventDefault(); if (confirm('{{ __('Are you sure?') }}')) { $('#delete-product-{{ $product->id }}').submit() }" class="btn btn-sm btn-danger">{{ __('Delete') }}</a>
                            <form action="{{ route('products.destroy', ['product' => $product->id]) }}" method="post" id="delete-product-{{ $product->id }}">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">{{ __('Nothing Found') }}</td>
                    </tr>
                @endforelse
            </table>
        </div>

            {{ $products->links() }}


        <br>
        <br>
        <br>
        <br>
        <br>

    </div>
@endsection
