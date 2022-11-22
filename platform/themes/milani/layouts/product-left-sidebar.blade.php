@php
    Theme::asset()->container('footer')->usePath()->add('jquery.theia.sticky-js', 'js/plugins/jquery.theia.sticky.js');
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
                        $category = \Botble\Ecommerce\Models\ProductCategory::where('id', request('categories')[0])->first();
                    }
                @endphp
                @if(request('categories'))
                    <div class="col-12 p-4 text-center" style="background: url('/storage/{{$category->image}}') center center no-repeat; background-size: cover; min-height: 50px;">
                        <div style="background-color: white; opacity: 0.4;">
                            {{$category->description}}
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
