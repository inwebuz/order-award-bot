<div class="form-group">
    <label for="first_name">{{ __('First name') }}</label>
    <input type="text" name="first_name" id="first_name" class="form-control" value="{{ old('first_name') ?? $order->first_name }}" required>
    @error('first_name')
    <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="last_name">{{ __('Last name') }}</label>
    <input type="text" name="last_name" id="last_name" class="form-control" value="{{ old('last_name') ?? $order->last_name }}" required>
    @error('last_name')
    <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="phone_number">{{ __('Phone number') }}</label>
    <input type="text" name="phone_number" id="phone_number" class="form-control" value="{{ old('phone_number') ?? $order->phone_number }}" required>
    @error('phone_number')
    <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="product_id">{{ __('Product') }}</label>
    <select name="product_id" id="product_id" class="form-control" required>
        @php $currentValue = old('product_id') ?? $order->product_id; @endphp
        @foreach($products as $product)
            <option value="{{ $product->id }}" @if($product->id == $currentValue) selected @endif>{{ $product->name }}</option>
        @endforeach
    </select>
    @error('product_id')
    <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="quantity">{{ __('Quantity') }}</label>
    <input type="number" name="quantity" id="quantity" class="form-control" value="{{ old('quantity') ?? $order->quantity }}" required>
    @error('quantity')
    <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="products_info">{{ __('Products info') }}</label>
    <textarea name="products_info" id="products_info" class="form-control" required>{{ old('products_info') ?? $order->products_info }}</textarea>
    @error('products_info')
    <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="status">{{ __('Status') }}</label>
    <select name="status" id="status" class="form-control" required>
        @php $currentValue = old('status') ?? $order->status; @endphp
        @foreach(\App\Order::statuses() as $key => $value)
            <option value="{{ $key }}" @if($key == $currentValue) selected @endif>{{ $value }}</option>
        @endforeach
    </select>
    @error('status')
    <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

