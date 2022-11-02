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
            <div class="row">
                <div class="col-lg-10">
                    {!! Theme::content() !!}
                </div>
                <div class="col-lg-2 primary-sidebar sticky-sidebar bg-grey-9">
                    <div class="widget-area">
                        {!! dynamic_sidebar('product_sidebar') !!}
                        {!! do_shortcode('[bullets][/bullets]') !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

{!! Theme::partial('footer') !!}
