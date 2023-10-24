@php
	$tot = $attributes->where('attribute_set_id', $set->id)->count();

    $nPe = 0;
    foreach($attributes->where('attribute_set_id', $set->id) as $attribute) {
        if (!$variationInfo->where('id', $attribute->id)->count()) {
            $nPe++;
        }
    }

    $show = $tot > 1 && ($tot - $nPe > 1);
@endphp
<div class="text-swatches-wrapper attribute-swatches-wrapper attribute-swatches-wrapper form-group product__attribute product__color" data-type="text" style="margin-bottom: 0px; padding-bottom: 0px; @if (!$show || $tot == 1) display: none; @endif">
    <label class="attribute-name"><b>{{ $set->title }}</b></label>
    <div class="attribute-values">
        <ul class="text-swatch attribute-swatch color-swatch">
            @foreach($attributes->where('attribute_set_id', $set->id) as $attribute)
                <li data-slug="{{ $attribute->slug }}"
                    data-id="{{ $attribute->id }}"
                    class="attribute-swatch-item @if (!$variationInfo->where('id', $attribute->id)->count() || $tot == 1) pe-none @endif">
                    <div>
                        <label>
                            <input class="product-filter-item"
                                type="radio"
                                name="attribute_{{ $set->slug }}_{{ $key }}"
                                value="{{ $attribute->id }}"
                                @if($tot == 1)
                                	checked
                                @else
                                	{{ $selected->where('id', $attribute->id)->count() ? 'checked' : '' }}
                               	@endif
                            >
                            <span>{{ $attribute->title }}</span>
                        </label>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>
