@php
    Theme::asset()->container('footer')->usePath()->add('jquery.theia.sticky-js', 'js/plugins/jquery.theia.sticky.js');
    $url = explode('/',Illuminate\Support\Facades\URL::current());
@endphp

{!! Theme::partial('header') !!}


<main class="main" id="main-section">
    @if (Theme::get('hasBreadcrumb', true))
        {!! Theme::partial('breadcrumb') !!}
    @endif

    <section class="mt-60 mb-60">
        <div class="container">
            <div class="row mb-4">
                @php

                	if(request('categories')){
	                	$array_cat = request('categories');
	                	if(isset($url[5])){
	                		$cat = end($array_cat);
	                	} else {
	                		$cat = $array_cat[0];
	                	}
	                    if($cat){
	                        $category = \Botble\Ecommerce\Models\ProductCategory::where('id', $cat)->first();
	                    }
	                }

                @endphp
                @if(request('categories'))
                    <div class="col-12 p-4 text-center" style="background: url('/storage/{{$category->image}}') center center no-repeat; background-size: cover; min-height: 50px;">
                        <div style="background-color: white; opacity: 1;">
                        	@if($category)
                                <h1 style="margin-bottom: 25px;">{{$category->name}}</h1>
                                <div class="category-description">
                            	    {!! htmlspecialchars_decode($category->description) !!}
                                </div>
                            @endif
                        </div>
                    </div>
                @elseif(request('q'))
                    <div class="col-12 p-4 text-center">
                        <div style="background-color: white; opacity: 1;">
                        	<h2>{{__('Search')}}: {{request('q')}}</h2>
                        </div>
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col-lg-3 primary-sidebar sticky-sidebar">


                    <form action="{{ isset($filterURL) ? $filterURL : route('public.products') }}" method="GET" id="products-filter-ajax">
                        @include(Theme::getThemeNamespace() . '::views/ecommerce/includes/filters')
                    </form>
                    <div class="widget-area">


{{--                        {!! dynamic_sidebar('product_sidebar') !!}--}}
                    </div>
                </div>
                <div class="col-lg-9">
                    {!! Theme::content() !!}
                </div>
            </div>
        </div>
    </section>
</main>

{!! Theme::partial('footer') !!}
