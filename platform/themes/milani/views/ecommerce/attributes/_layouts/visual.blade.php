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
<div class="visual-swatches-wrapper attribute-swatches-wrapper form-group product__attribute product__color" data-type="visual" style="margin-bottom: 0px; padding-bottom: 0px; @if (!$show || $tot == 1) display: none; @endif">
    <label class="attribute-name"><b>{{ $set->title }}</b></label>
    <div class="attribute-values">
        <ul class="visual-swatch color-swatch attribute-swatch">

            @foreach($attributes->where('attribute_set_id', $set->id) as $attribute)
                <li data-slug="{{ $attribute->slug }}"
                    data-id="{{ $attribute->id }}"
                    class="attribute-swatch-item @if (!$variationInfo->where('id', $attribute->id)->count() || $tot == 1) pe-none @endif"
                    title="{{ $attribute->title }}">
                    <div class="custom-radio">
                        <label>
                            <input class="form-control product-filter-item"
                                type="radio"
                                name="attribute_{{ $set->slug }}_{{ $key }}"
                                value="{{ $attribute->id }}"
                                @if($tot == 1)
                                	checked
                                @else
                                	{{ $selected->where('id', $attribute->id)->count() ? 'checked' : '' }}
                               	@endif
                           >
				<span style="{{ $attribute->getAttributeStyle($set, $productVariations) }} border: 1px solid black;"></span>
                        </label>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>
