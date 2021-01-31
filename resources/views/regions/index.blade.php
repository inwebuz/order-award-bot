@extends('layouts.app')

@section('title', __('Regions'))

@section('content')
    <div class="container">

        @if(session('success'))
            <div class="alert alert-success">
                {{ __(session('success')) }}
            </div>
        @endif

        <div class="my-4">
            <a href="{{ route('regions.create') }}" class="btn btn-lg btn-info">
                {{ __('Add region') }}
            </a>
        </div>


        <h1>{{ __('Regions') }}</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-light table-hover">
                <tr class="table-active">
                    <th>
                        {{ __('Title') }}
                    </th>
                    <th></th>
                </tr>
                @forelse($regions as $region)
                    <tr>
                        <td>{{ $region->name }}</td>
                        <td class="text-nowrap">
                            <a href="{{ route('regions.edit', ['region' => $region->id]) }}" class="btn btn-sm btn-warning">{{ __('Edit') }}</a>
                            <a href="#" onclick="event.preventDefault(); if (confirm('{{ __('Are you sure?') }}')) { $('#delete-region-{{ $region->id }}').submit() }" class="btn btn-sm btn-danger">{{ __('Delete') }}</a>
                            <form action="{{ route('regions.destroy', ['region' => $region->id]) }}" method="post" id="delete-region-{{ $region->id }}">
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
