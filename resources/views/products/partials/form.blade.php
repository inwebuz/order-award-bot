<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="name">{{ __('Title') }}</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') ?? $product->name }}" >
            @error('name')
            <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="uz_name">{{ __('Title') }} (UZ)</label>
            <input type="text" name="translations[uz][name]" id="uz_name" class="form-control" value="{{ old('translations.uz.name') ?? $product->t('uz')->content['name'] ?? $product->name }}" required>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="name">{{ __('Measurement unit') }}</label>
            <input type="text" name="units" id="units" class="form-control" value="{{ old('units') ?? $product->units }}" >
            @error('units')
            <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="uz_units">{{ __('Measurement unit') }} (UZ)</label>
            <input type="text" name="translations[uz][units]" id="uz_units" class="form-control" value="{{ old('translations.uz.units') ?? $product->t('uz')->content['units'] ?? $product->units }}" required>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="button_text">{{ __('Button text') }}</label>
            <input type="text" name="button_text" id="button_text" class="form-control" value="{{ old('button_text') ?? $product->button_text }}" >
            @error('button_text')
            <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="uz_button_text">{{ __('Button text') }} (UZ)</label>
            <input type="text" name="translations[uz][button_text]" id="uz_button_text" class="form-control" value="{{ old('translations.uz.button_text') ?? $product->t('uz')->content['button_text'] ?? $product->button_text }}" required>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="description">{{ __('Description') }}</label>
            <textarea type="text" name="description" id="description" class="form-control" >{{ old('description') ?? $product->description }}</textarea>
            @error('description')
            <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="uz_description">{{ __('Description') }} (UZ)</label>
            <textarea type="text" name="translations[uz][description]" id="uz_description" class="form-control">{{ old('translations.uz.description') ?? $product->t('uz')->content['description'] ?? $product->description }}</textarea>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="price">{{ __('Price') }}</label>
    <input type="text" name="price" id="price" class="form-control" value="{{ old('price') ?? $product->price }}" >
    @error('price')
    <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

{{-- <div class="form-group">
    <label for="catalog_id">{{ __('Category') }}</label>
    <select name="catalog_id" id="catalog_id" class="form-control">
        @php $currentValue = old('catalog_id') ?? $product->catalog_id; @endphp
        @foreach($categories as $category)
            <option value="{{ $category->id }}" @if($category->id == $currentValue) selected @endif>{{ $category->title }}</option>
        @endforeach
    </select>
    @error('catalog_id')
    <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div> --}}

{{-- <div class="form-group">
    <label for="image">{{ __('Image') }}</label>
    @if($product->image)
        <div class="mb-4">
            <img src="{{ Storage::disk('public')->url($product->image) }}" alt="" style="max-width: 200px; height: auto;">
        </div>
    @endif
    <div>
        <input type="file" name="image" id="image">
    </div>
    @error('image')
    <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div> --}}
