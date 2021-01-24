@extends('layouts.app')

@section('title', __('Reviews'))

@section('content')
    <div class="container">

        @if(session('success'))
            <div class="alert alert-success">
                {{ __(session('success')) }}
            </div>
        @endif

        <div class="my-4">
            <a href="{{ route('reviews.create') }}" class="btn btn-lg btn-info">
                {{ __('Add review') }}
            </a>
        </div>


        <h1>{{ __('Reviews') }}</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-light table-hover">
                <tr class="table-active">
                    <th>
                        {{ __('Title') }}
                    </th>
                    <th>
                        {{ __('Status') }}
                    </th>
                    <th></th>
                </tr>
                @forelse($reviews as $review)
                    <tr>
                        <td>{{ $review->name }}</td>
                        <td>{{ $review->status_text }}</td>
                        <td class="text-nowrap">
                            <a href="{{ route('reviews.edit', ['review' => $review->id]) }}" class="btn btn-sm btn-warning">{{ __('Edit') }}</a>
                            <a href="#" onclick="event.preventDefault(); if (confirm('{{ __('Are you sure?') }}')) { $('#delete-review-{{ $review->id }}').submit() }" class="btn btn-sm btn-danger">{{ __('Delete') }}</a>
                            <form action="{{ route('reviews.destroy', ['review' => $review->id]) }}" method="post" id="delete-review-{{ $review->id }}">
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


        <br>
        <br>
        <br>
        <br>
        <br>

    </div>
@endsection
