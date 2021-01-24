<div class="form-group">
    <label for="title">{{ __('Title') }}</label>
    <input type="text" name="title" id="title" class="form-control" value="{{ old('title') ?? $product->title }}" >
    @error('title')
    <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="uz_title">{{ __('Title') }} (UZ)</label>
    <input type="text" name="translations[uz][title]" id="uz_title" class="form-control" value="{{ old('translations.uz.title') ?? $product->t('uz')->content['title'] ?? $product->title }}" required>
</div>

<div class="form-group">
    <label for="description">{{ __('Measurement unit') }}</label>
    <input type="text" name="description" id="description" class="form-control" value="{{ old('description') ?? $product->description }}" >
    @error('description')
    <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="uz_description">{{ __('Measurement unit') }} (UZ)</label>
    <input type="text" name="translations[uz][description]" id="uz_description" class="form-control" value="{{ old('translations.uz.description') ?? $product->t('uz')->content['description'] ?? $product->description }}">
</div>

<div class="form-group">
    <label for="price">{{ __('Price') }}</label>
    <input type="text" name="price" id="price" class="form-control" value="{{ old('price') ?? $product->price }}" >
    @error('price')
    <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
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
</div>

<div class="form-group">
    <label for="url">{{ __('Image') }}</label>
    @if($product->url)
        <div class="mb-4">
            <img src="{{ Storage::disk('telegrambot')->url($product->url) }}" alt="" style="max-width: 200px; height: auto;">
        </div>
    @endif
    <div>
        <input type="file" name="url" id="url">
    </div>
    @error('url')
    <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
