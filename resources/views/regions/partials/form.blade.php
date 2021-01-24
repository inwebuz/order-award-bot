<div class="form-group">
    <label for="form_name">{{ __('Title') }}</label>
    <input type="text" name="name" id="form_name" class="form-control" value="{{ old('name') ?? $region->name }}" required>
    @error('name')
    <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
