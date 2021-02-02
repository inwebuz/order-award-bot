<div class="form-group">
    <label for="name">{{ __('Title') }}</label>
    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') ?? $gallery->name }}" >
    @error('name')
    <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="image">{{ __('Image') }}</label>
    @if($gallery->image)
        <div class="mb-4">
            <img src="{{ Storage::disk('public')->url($gallery->image) }}" alt="" style="max-width: 200px; height: auto;">
        </div>
    @endif
    <div>
        <input type="file" name="image" id="image">
    </div>
    @error('image')
    <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
