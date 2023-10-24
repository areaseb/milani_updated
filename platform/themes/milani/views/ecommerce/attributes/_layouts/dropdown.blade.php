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
<div class="dropdown-swatches-wrapper attribute-swatches-wrapper" data-type="dropdown" @if(!$show || $tot <= 1) style="display: none;" @endif>
    <div class="attribute-name"><b>{{ $set->title }}</b></div>
    <div class="attribute-values">
        <div class="dropdown-swatch">
            <label>
                <select class="form-control product-filter-item" id="{{$set->id}}">
                    <option value="">{{ __('Select') . ' ' . strtolower($set->title) }}</option>
                    @foreach($attributes->where('attribute_set_id', $set->id) as $attribute)
                        <option
                                value="{{ $attribute->id }}"
                                data-id="{{ $attribute->id }}"
                                @if($tot == 1)
                                	selected
                                @else
	                                {{ $selected->where('id', $attribute->id)->count() ? 'selected' : '' }}
	                                @if (!$variationInfo->where('id', $attribute->id)->count()) disabled="disabled" @endif
	                            @endif
	                    >
                            {{ $attribute->title }}
                        </option>
                    @endforeach
                </select>
            </label>
        </div>
    </div>
</div>
