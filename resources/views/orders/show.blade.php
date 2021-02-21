@extends('layouts.app')

@section('title', __('Order') . '#' . $order->id)
@section('body_class', 'payment-page')

@section('content')
    <div class="container">

        <h1>
            {{ __('Order') }} #{{ $order->id }}
        </h1>

        <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                    <td>
                        <strong>{{ __('Order number') }}</strong>
                    </td>
                    <td>
                        {{ $order->id }}
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>{{ __('Date') }}</strong>
                    </td>
                    <td class="text-nowrap">
                        {{ Helper::formatDateTime($order->created_at) }}
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>{{ __('Status') }}</strong>
                    </td>
                    <td>
                        {{ $order->status_text }}
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>{{ __('Name') }}</strong>
                    </td>
                    <td>
                        {{ $order->first_name }} {{ $order->last_name }}
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>{{ __('Phone number') }}</strong>
                    </td>
                    <td>
                        {{ $order->phone_number }}
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>{{ __('Product') }}</strong>
                    </td>
                    <td>
                        {{ $order->product->name }}
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>{{ __('Quantity') }}</strong>
                    </td>
                    <td>
                        {{ $order->quantity }}
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>{{ __('Products info') }}</strong>
                    </td>
                    <td>
                        {{ $order->products_info }}
                    </td>
                </tr>
                @if ($order->image)
                    <tr>
                        <td>
                            <strong>{{ __('Photo') }}</strong>
                        </td>
                        <td>
                            <a class="d-inline-block" href="{{ Storage::url($order->image) }}" target="_blank">
                                <img src="{{ Storage::url($order->image) }}" alt="" style="max-width: 200px;" class="img-fluid">
                            </a>
                            @if ($order->additional_images)
                                @foreach ($order->getAdditionalImagesArray() as $image)
                                    <a class="d-inline-block" href="{{ Storage::url($image) }}" target="_blank">
                                        <img src="{{ Storage::url($image) }}" alt="" style="max-width: 200px;" class="img-fluid">
                                    </a>
                                @endforeach
                            @endif
                        </td>
                    </tr>
                @endif
                <tr>
                    <td>
                        <strong>{{ __('Total') }}</strong>
                    </td>
                    <td class="text-nowrap">
                        {{ BotHelper::formatPrice($order->total) }}
                    </td>
                </tr>
                <tr class="d-print-none">
                    <td>
                        <strong>&nbsp;</strong>
                    </td>
                    <td>
                        <a href="{{ route('orders.edit', ['order' => $order->id]) }}" class="btn btn-sm btn-warning">{{ __('Edit') }}</a>
                        <a href="#" onclick="event.preventDefault(); if (confirm('{{ __('Are you sure?') }}')) { $('#delete-order-{{ $order->id }}').submit() }" class="btn btn-sm btn-danger">{{ __('Delete') }}</a>
                        <form action="{{ route('orders.destroy', ['order' => $order->id]) }}" method="post" id="delete-order-{{ $order->id }}">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
            </table>
        </div>

        @if ($order->status == \App\Order::STATUS_OPEN)
            <div class="py-4 d-print-none">
                <form action="{{ route('orders.close', ['order' => $order->id]) }}" method="post">
                    @csrf
                    <div class="form-group">
                        <label for="order_noitification">{{ __('Notification') }}</label>
                        <textarea class="form-control" name="notification" id="order_noitification">{{ __('Your order #:id is completed', ['id' => $order->id]) }}</textarea>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary">{{ __('Set the order completed') }}</button>
                    </div>
                </form>
            </div>
        @endif

        <div class="py-4 d-print-none">
            <a href="/orders" class="btn btn-info">{{ __('Back') }}</a>
        </div>

    </div>
@endsection
