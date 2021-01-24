<div class="form-group">
    <label for="form_name">{{ __('Name') }}</label>
    <input type="text" name="name" id="form_name" class="form-control" value="{{ old('name') ?? $review->name }}" required>
    @error('name')
    <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="form_message">{{ __('Message') }}</label>
    <textarea type="text" name="message" id="form_message" class="form-control" required>{{ old('message') ?? $review->message }}</textarea>
    @error('message')
    <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="form_status">{{ __('Status') }}</label>
    <select name="status" id="form_status" class="form-control">
        @php
            $currentStatus = old('status') ?? $review->status;
        @endphp
        @foreach($statuses as $statusKey => $statusText)
            <option value="{{ $statusKey }}" @if($statusKey == $currentStatus) selected @endif>{{ $statusText }}</option>
        @endforeach
    </select>
    @error('status')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
