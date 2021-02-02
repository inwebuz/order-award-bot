@extends('layouts.app')

@section('title', __('Our products'))

@section('content')
    <div class="container">

        @if(session('success'))
            <div class="alert alert-success">
                {{ __(session('success')) }}
            </div>
        @endif

        <div class="my-4">
            <a href="{{ route('galleries.create') }}" class="btn btn-lg btn-info">
                {{ __('Add') }}
            </a>
        </div>


        <h1>{{ __('Our products') }}</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-light table-hover">
                <tr class="table-active">
                    <th>
                        {{ __('Name') }}
                    </th>
                    <th>
                        {{ __('Image') }}
                    </th>
                    <th></th>
                </tr>
                @forelse($galleries as $gallery)
                    <tr>
                        <td>{{ $gallery->name }} </td>
                        <td>
                            @if($gallery->image)
                                <img src="{{ Storage::disk('public')->url($gallery->image) }}" alt="" style="max-width: 100px; height: auto;">
                            @endif
                        </td>
                        <td class="text-nowrap">
                            <a href="{{ route('galleries.edit', ['gallery' => $gallery->id]) }}" class="btn btn-sm btn-warning">{{ __('Edit') }}</a>
                            <a href="#" onclick="event.preventDefault(); if (confirm('{{ __('Are you sure?') }}')) { $('#delete-gallery-{{ $gallery->id }}').submit() }" class="btn btn-sm btn-danger">{{ __('Delete') }}</a>
                            <form action="{{ route('galleries.destroy', ['gallery' => $gallery->id]) }}" method="post" id="delete-gallery-{{ $gallery->id }}">
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

            {{ $galleries->links() }}


        <br>
        <br>
        <br>
        <br>
        <br>

    </div>
@endsection
