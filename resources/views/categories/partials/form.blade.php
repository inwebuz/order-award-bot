<div class="form-group">
    <label for="title">{{ __('Title') }} (RU)</label>
    <input type="text" name="title" id="title" class="form-control" value="{{ old('title') ?? $category->title }}" required>
    @error('title')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="uz_title">{{ __('Title') }} (UZ)</label>
    <input type="text" name="translations[uz][title]" id="uz_title" class="form-control" value="{{ old('translations.uz.title') ?? $category->t('uz')->content['title'] ?? $category->title }}" required>
</div>

<div class="form-group">
    <label for="parent_id">{{ __('Parent category') }}</label>
    <select name="parent_id" id="parent_id" class="form-control">
        <option value="">-</option>
        @foreach($categories as $parentCategory)
            <option value="{{ $parentCategory->id }}" @if($parentCategory->id == $category->parent_id) selected @endif>{{ $parentCategory->title }}</option>
        @endforeach
    </select>
    @error('parent_id')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
