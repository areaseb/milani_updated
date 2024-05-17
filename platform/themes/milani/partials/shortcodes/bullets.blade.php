@php
	// $bullets = \Botble\Ecommerce\Models\ProductAttributeSet::where('title', 'Bullet')->pluck('id')->toArray();
	$bullets = array();
	
	if(isset($_GET['s'])){
		$product = \Botble\Ecommerce\Models\Product::where('sku', $_GET['s'])->first();
		if($product){
			$bullets[] = $product->bullet_1;
			$bullets[] = $product->bullet_2;
			$bullets[] = $product->bullet_3;
			$bullets[] = $product->bullet_4;
			$bullets[] = $product->bullet_5;
		}
	} else {
		$url = explode('/',Illuminate\Support\Facades\URL::current());
		if(isset($url[4])){
	    	$currenturl = $url[4];
	    	$product_slug = \Botble\Slug\Models\Slug::where('key', $currenturl)->first();
	    	if($product_slug){
	    		$product = \Botble\Ecommerce\Models\Product::where('id', $product_slug->reference_id)->first();
	    	}
	   	}
		
		if($product){
			$bullets[] = $product->bullet_1;
			$bullets[] = $product->bullet_2;
			$bullets[] = $product->bullet_3;
			$bullets[] = $product->bullet_4;
			$bullets[] = $product->bullet_5;
		}	
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
                    @if($bullet)
                    	<li>{!! nl2br($bullet) !!}</li>
                    @endif
                @endforeach
            @endif
    	</ul>
    </div>
</div>
