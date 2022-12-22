@php
	$bullets = \Botble\Ecommerce\Models\ProductAttributeSet::where('title', 'Bullet')->pluck('id')->toArray();
@endphp

<div class="mt-20">
    <div class="widget-header position-relative mb-20 pb-10">
        <h5 class="widget-title mb-10">CARATTERISTICHE PRINCIPALI</h5>
        <div class="bt-1 border-color-1"></div>
    </div>
    <div class="custome-checkbox _mCS_1">
    	<ul class="ps-list--categories">
            @if(!is_null($attributes))
                @foreach($attributes as $attribute)
                    @if(in_array($attribute->attribute_set_id, $bullets))
                        <li>{{ $attribute->title }}</li>
                    @endif
                @endforeach
            @endif
    	</ul>
    </div>
</div>
