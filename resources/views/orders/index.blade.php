@extends('layouts.app')

@section('title', __('Orders'))

@section('content')
    <div class="container">

        @if(session('success'))
            <div class="alert alert-success">
                {{ __(session('success')) }}
            </div>
        @endif

        <div class="my-4">
            <a href="{{ route('orders.create') }}" class="btn btn-lg btn-info">
                {{ __('Add order') }}
            </a>
            <a href="{{ route('orders.upload') }}" class="btn btn-lg btn-info">
                {{ __('Orders upload') }}
            </a>
        </div>


        <h1>{{ __('Orders') }}</h1>

        <form action="{{ route('orders.index') }}" id="order-filter-form">

            <div class="row">
                <div class="col-6 col-md-4">
                    <div class="form-group">
                        <label for="period_from">{{ __('Period from') }}</label>
                        <input type="text" name="period_from" class="form-control datepicker-here" data-date-format="dd.mm.yyyy" value="{{ $filter['period_from']->format('d.m.Y') }}">
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="form-group">
                        <label for="period_to">{{ __('Period to') }}</label>
                        <input type="text" name="period_to" class="form-control datepicker-here" data-date-format="dd.mm.yyyy" value="{{ $filter['period_to']->format('d.m.Y') }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6 col-md-4">
                    <div class="form-group">
                        <label for="id">{{ __('Order number') }}</label>
                        <input type="text" name="id" class="form-control" value="{{ $filter['id'] }}">
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="form-group">
                        <label for="status">{{ __('Status') }}</label>
                        <select name="status" class="form-control">
                            <option value="-">-</option>
                            @foreach(\App\Order::statuses() as $key => $value)
                                <option value="{{ $key }}" @if((string)$key == $filter['status']) selected @endif>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-6 col-md-4">
                    <div class="form-group">
                        <label for="first_name">{{ __('First name') }}</label>
                        <input type="text" name="first_name" class="form-control" value="{{ $filter['first_name'] }}">
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="form-group">
                        <label for="last_name">{{ __('Last name') }}</label>
                        <input type="text" name="last_name" class="form-control" value="{{ $filter['last_name'] }}">
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="form-group">
                        <label for="phone_number">{{ __('Phone number') }}</label>
                        <input type="text" name="phone_number" class="form-control" value="{{ $filter['phone_number'] }}">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <button class="btn btn-primary" type="submit">{{ __('Применить фильтр') }}</button>
            </div>
        </form>

        <div class="my-4">
            <h5>{{ __('Statistics for the selected period') }}</h5>
            <div class="my-2">
                <div>
                    {{ __('Total orders') }}: {{ $stats['all']['quantity'] }}. {{ __('Sum') }}: {{ BotHelper::formatPrice($stats['all']['sum']) }}
                </div>
                <div class="ml-4">
                    {{ __('With status open') }}: {{ $stats['open']['quantity'] }}. {{ __('Sum') }}: {{ BotHelper::formatPrice($stats['open']['sum']) }}
                </div>
                <div class="ml-4">
                    {{ __('With status close') }}: {{ $stats['close']['quantity'] }}. {{ __('Sum') }}: {{ BotHelper::formatPrice($stats['close']['sum']) }}
                </div>
            </div>
            <div class="my-2">
                <a href="{{ route('orders.download', request()->all()) }}" class="btn btn-sm btn-success">{{ __('Download') }} XLSX</a>
            </div>
        </div>

        <div class="my-4">
            <h4>{{ __('Orders') }}</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-light table-hover">
                    <tr class="table-active">
                        <th>
                            {{ __('Order number') }}
                        </th>
                        <th>
                            {{ __('Name') }}
                        </th>
                        {{-- <th>
                            {{ __('Products Info') }}
                        </th> --}}
                        <th>
                            {{ __('Product') }}
                        </th>
                        <th>
                            {{ __('Total') }}
                        </th>
                        <th>
                            {{ __('Status') }}
                        </th>
                        <th></th>
                    </tr>
                    @forelse($orders as $order)
                        <tr>
                            <td class="text-center">
                                <strong>{{ $order->id }}</strong>
                                <br>
                                <em>{{ Helper::formatDateTime($order->created_at) }}</em>
                            </td>
                            <td>
                                <span class="text-nowrap">{{ $order->first_name }} {{ $order->last_name }}</span>
                                <br>
                                <em class="text-nowrap">{{ $order->phone_number }}</em>
                            </td>
                            {{-- <td>{{ $order->products_info }}</td> --}}
                            <td>
                                <span>{{ $order->product->name }}</span>
                                <br>
                                <strong class="text-nowrap">&times; {{ $order->quantity }}</strong>
                            </td>
                            <td class="text-nowrap">{{ BotHelper::formatPrice($order->total) }}</td>
                            <td>{{ $order->status_text }}</td>
                            <td class="text-nowrap">
                                <a href="{{ route('orders.show', ['order' => $order->id]) }}" class="btn btn-sm btn-info">{{ __('View more') }}</a>
                                <a href="{{ route('orders.edit', ['order' => $order->id]) }}" class="btn btn-sm btn-warning">{{ __('Edit') }}</a>
                                <a href="#" onclick="event.preventDefault(); if (confirm('{{ __('Are you sure?') }}')) { $('#delete-order-{{ $order->id }}').submit() }" class="btn btn-sm btn-danger">{{ __('Delete') }}</a>
                                <form action="{{ route('orders.destroy', ['order' => $order->id]) }}" method="post" id="delete-order-{{ $order->id }}">
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
            {{ $orders->withQueryString()->links() }}
        </div>

        <br>
        <br>
        <br>
        <br>
        <br>

    </div>
@endsection
