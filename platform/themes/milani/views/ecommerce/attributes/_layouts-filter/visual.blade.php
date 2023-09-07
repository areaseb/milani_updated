@if(substr($set->title, -1) != '2' && substr($set->title, -1) != '3')
<div class="col-12 mb-4 widget-filter-item" data-type="visual">
    <h5 class="mb-20 widget__title" data-title="{{ $set->title }}">{{ __('By :name', ['name' => str_replace('1', '', $set->title)]) }}</h5>
    <ul class="list-filter ps-custom-scrollbar_">
        @foreach($attributes->where('attribute_set_id', $set->id) as $attribute)
            <li data-slug="{{ $attribute->slug }}"
                data-toggle="tooltip"
                data-placement="top"
                title="{{ $attribute->title }}"
                class="mx-1">
                <div class="custom-checkbox">
                    <label>
                        <input class="form-control product-filter-item" type="checkbox" name="attributes[]" value="{{ $attribute->id }}" {{ in_array($attribute->id, $selected) ? 'checked' : '' }}>
			            <span style="{{ $attribute->getAttributeStyle() }} border: 1px solid black;"></span>
                    </label>
                </div>
            </li>
        @endforeach
    </ul>
</div>
@endif
