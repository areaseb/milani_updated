<div class="col-12 mb-4 widget-filter-item" data-type="text">
    <h5 class="mb-15 widget__title" data-title="{{ $set->title }}" >{{ __('By :name', ['name' => $set->title]) }}</h5>
    <div class="list-filter size-filter font-small ps-custom-scrollbar_">
        @foreach($attributes->where('attribute_set_id', $set->id) as $attribute)
            <li data-slug="{{ $attribute->slug }}">
                <label>
                    <input class="product-filter-item" type="checkbox" name="attributes[]" value="{{ $attribute->id }}" {{ in_array($attribute->id, $selected) ? 'checked' : '' }}>
                    <span>{{ $attribute->title }}</span>
                </label>
            </li>
        @endforeach
    </div>
</div>
