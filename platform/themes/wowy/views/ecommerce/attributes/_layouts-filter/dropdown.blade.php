<div class="col-12 mb-4 widget-filter-item" data-type="dropdown">
    <h5 class="mb-15 widget__title" data-title="{{ $set->title }}" >{{ __('By :name', ['name' => $set->title]) }}</h5>
    <div class="list-filter size-filter font-small ps-custom-scrollbar_">
        <div class="attribute-values">
            <div class="dropdown-swatch">
                <label>
                    <select class="form-control product-filter-item" name="attributes[]">
                        <option value="">{{ __('-- Select --') }}</option>
                        @foreach($attributes->where('attribute_set_id', $set->id) as $attribute)
                            <option value="{{ $attribute->id }}" {{ in_array($attribute->id, $selected) ? 'selected' : '' }}>{{ $attribute->title }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
        </div>
    </div>
</div>
