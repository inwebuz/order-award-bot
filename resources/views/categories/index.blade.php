@extends('layouts.app')

@section('title', __('Categories'))

@section('content')
    <div class="container">

        @if(session('success'))
            <div class="alert alert-success">
                {{ __(session('success')) }}
            </div>
        @endif

        <div class="my-4">
            <a href="{{ route('categories.create') }}" class="btn btn-lg btn-info">
                {{ __('Add category') }}
            </a>
        </div>


        <h1>{{ __('Categories') }}</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-light table-hover">
                <tr class="table-active">
                    <th>
                        {{ __('Title') }}
                    </th>
                    <th>
                        {{ __('Parent category') }}
                    </th>
                    <th></th>
                </tr>
                @forelse($categories as $category)
                    <tr>
                        <td>{{ $category->title }}</td>
                        <td>{{ Helper::categoryParentTitle($category) }}</td>
                        <td class="text-nowrap">
                            <a href="{{ route('categories.edit', ['category' => $category->id]) }}" class="btn btn-sm btn-warning">{{ __('Edit') }}</a>
                            <a href="#" onclick="event.preventDefault(); if (confirm('{{ __('Are you sure?') }}')) { $('#delete-category-{{ $category->id }}').submit() }" class="btn btn-sm btn-danger">{{ __('Delete') }}</a>
                            <form action="{{ route('categories.destroy', ['category' => $category->id]) }}" method="post" id="delete-category-{{ $category->id }}">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">{{ __('Nothing found') }}</td>
                    </tr>
                @endforelse
            </table>
        </div>


        <br>
        <br>
        <br>
        <br>
        <br>

    </div>
@endsection
