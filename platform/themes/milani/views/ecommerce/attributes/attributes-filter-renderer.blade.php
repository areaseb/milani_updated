@foreach($attributeSets as $data)
	@if($data['attributeSet']->slug != 'portata-massima')
	    @if(view()->exists(Theme::getThemeNamespace(). '::views.ecommerce.attributes._layouts-filter.' . $data['attributeSet']->display_layout))
	        @include(Theme::getThemeNamespace(). '::views.ecommerce.attributes._layouts-filter.' . $data['attributeSet']->display_layout, [
	            'set'        => $data['attributeSet'],
	            'attributes' => $data['attributes'],
	            'selected'   => (array)request()->query('attributes', []),
	        ])
	    @else
	        @include(Theme::getThemeNamespace(). '::views.ecommerce.attributes._layouts.dropdown', [
	            'set'        => $data['attributeSet'],
	            'attributes' => $data['attributes'],
	            'selected'   => (array)request()->query('attributes', []),
	        ])
	    @endif
	@endif
@endforeach
