@php
	// $bullets = \Botble\Ecommerce\Models\ProductAttributeSet::where('title', 'Bullet')->pluck('id')->toArray();
	$bullets = array();
	$url = explode('/',Illuminate\Support\Facades\URL::current());
	if(isset($url[4])){
    	$currenturl = $url[4];
    	$product = \Botble\Slug\Models\Slug::where('key', $currenturl)->first()->reference_id;
   	}
	
	for($i = 1; $i <= 5; $i++){
		$bullets[] = \Botble\Ecommerce\Models\Product::where('id', $product)->pluck('bullet_'.$i);
	}
@endphp

<div class="mt-20">
    <div class="widget-header position-relative mb-20 pb-10">
        <h5 class="widget-title mb-10">CARATTERISTICHE PRINCIPALI</h5>
        <div class="bt-1 border-color-1"></div>
    </div>
    <div class="custome-checkbox _mCS_1">
    	<ul style="list-style-type: disc; padding-left: calc(var(--bs-gutter-x)/ 2);" class="ps-list--categories_">
            @if(!is_null($bullets))
                @foreach($bullets as $bullet)
                    @if($bullet[0])
                    	<li>{!! nl2br($bullet[0]) !!}</li>
                    @endif
                @endforeach
            @endif
    	</ul>
    </div>
</div>
