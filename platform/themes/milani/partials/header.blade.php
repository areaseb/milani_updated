<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <meta name="csrf-token" content="{{ csrf_token() }}">
        @php
        	$url = explode('/',Illuminate\Support\Facades\URL::current());
        @endphp
        @if(isset($url[3]) && $url[3] == 'blog' && isset($url[4]) && $url[4] != 'come-illuminare-un-gazebo-in-legno' && $url[4] != 'come-decorare-un-tavolo-da-giardino')
        	<meta name="robots" content="noindex, nofollow">
        @elseif(isset($url[3]) && $url[3] == 'blog')
        	<meta name="robots" content="noindex, nofollow">
        @endif

        {{-- <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family={{ urlencode(theme_option('font_text', 'Poppins')) }}:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
        --}}
		{{--
		<link rel="preload" href="https://fonts.googleapis.com" as="style" onload="this.onload=null;this.rel='stylesheet'">
		<noscript><link rel="stylesheet" href="https://fonts.googleapis.com"></noscript>
		<link rel="preload" href="https://fonts.gstatic.com" as="style" onload="this.onload=null;this.rel='stylesheet'" crossorigin>
		<noscript><link rel="stylesheet" href="https://fonts.gstatic.com" crossorigin></noscript>
		--}}
		<link rel="preload" href="https://fonts.googleapis.com/css2?family={{ urlencode(theme_option('font_text', 'Poppins')) }}:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
		<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family={{ urlencode(theme_option('font_text', 'Poppins')) }}:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap"></noscript>

        <script async src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.6/dist/cdn.min.js"></script>

        <style>
            :root {
                --font-text: {{ theme_option('font_text', 'Poppins') }}, sans-serif;
                --color-brand: {{ theme_option('color_brand', '#5897fb') }};
                --color-brand-2: {{ theme_option('color_brand_2', '#3256e0') }};
                --color-primary: {{ theme_option('color_primary', '#3f81eb') }};
                --color-secondary: {{ theme_option('color_secondary', '#41506b') }};
                --color-warning: {{ theme_option('color_warning', '#ffb300') }};
                --color-danger: {{ theme_option('color_danger', '#ff3551') }};
                --color-success: {{ theme_option('color_success', '#3ed092') }};
                --color-info: {{ theme_option('color_info', '#18a1b7') }};
                --color-text: {{ theme_option('color_text', '#4f5d77') }};
                --color-heading: {{ theme_option('color_heading', '#222222') }};
                --color-grey-1: {{ theme_option('color_grey_1', '#111111') }};
                --color-grey-2: {{ theme_option('color_grey_2', '#242424') }};
                --color-grey-4: {{ theme_option('color_grey_4', '#90908e') }};
                --color-grey-9: {{ theme_option('color_grey_9', '#f4f5f9') }};
                --color-muted: {{ theme_option('color_muted', '#8e8e90') }};
                --color-body: {{ theme_option('color_body', '#4f5d77') }};
            }
        </style>

        {!! Theme::header() !!}

        @php
            $headerStyle = theme_option('header_style') ?: '';
            $page = Theme::get('page');
            if ($page) {
                $headerStyle = $page->getMetaData('header_style', true) ?: $headerStyle;
            }
            $headerStyle = ($headerStyle && in_array($headerStyle, array_keys(get_layout_header_styles()))) ? $headerStyle : '';
        @endphp
        
        <script async type="text/javascript">
			var _iub = _iub || [];
			_iub.csConfiguration = {"askConsentAtCookiePolicyUpdate":true,"floatingPreferencesButtonDisplay":"bottom-left","perPurposeConsent":true,"siteId":2335001,"whitelabel":false,"cookiePolicyId":64090603,"lang":"it", "banner":{ "acceptButtonDisplay":true,"backgroundOverlay":true,"closeButtonRejects":true,"customizeButtonDisplay":true,"explicitWithdrawal":true,"listPurposes":true,"position":"bottom","showTitle":false }};
		</script>
		<script async type="text/javascript" src="https://cs.iubenda.com/autoblocking/2335001.js"></script>
		<script async type="text/javascript" src="//cdn.iubenda.com/cs/iubenda_cs.js" charset="UTF-8" async></script>
		
		<!-- Google Tag Manager -->
		<script>
			(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
			new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
			j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
			'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
			})(window,document,'script','dataLayer','GTM-MPBC629');
		</script>
		<!-- End Google Tag Manager -->
				
		<!-- Facebook Pixel Code --> 
		<script>
		  !function(f,b,e,v,n,t,s)
		  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
		  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
		  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
		  n.queue=[];t=b.createElement(e);t.async=!0;
		  t.src=v;s=b.getElementsByTagName(e)[0];
		  s.parentNode.insertBefore(t,s)}(window, document,'script',
		  'https://connect.facebook.net/en_US/fbevents.js');
		  fbq('init', '119730032289920');
		  fbq('track', 'PageView');
		</script> 
		<noscript> 
			<img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=119730032289920&amp;ev=PageView&amp;noscript=1"> 
		</noscript> 
		<!-- End Facebook Pixel Code -->
		
		<!-- Start of milanihomehelp Zendesk Widget script --> 
  		<script async id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key=8a656522-7db5-4312-b6da-15a333614096"></script> 
  		<!-- End of milanihomehelp Zendesk Widget script --> 
  		
  		<!-- Pinterest Tag --> 
		<script>
			!function(e){if(!window.pintrk){window.pintrk = function () {
			window.pintrk.queue.push(Array.prototype.slice.call(arguments))};var
			n=window.pintrk;n.queue=[],n.version="3.0";var
			t=document.createElement("script");t.async=!0,t.src=e;var
			r=document.getElementsByTagName("script")[0];
			r.parentNode.insertBefore(t,r)}}("https://s.pinimg.com/ct/core.js");
			pintrk('load', '2613038424263', {em: '<user_email_address>'});
			pintrk('page');
		</script> 
		<noscript> 
		   <img height="1" width="1" style="display:none;" alt="" src="https://ct.pinterest.com/v3/?event=init&amp;tid=2613038424263&amp;pd[em]=<hashed_email_address>&amp;noscript=1"> 
		</noscript> 
		<!-- end Pinterest Tag --> 
		
		<!-- TrustBox script -->
		<script async type="text/javascript" src="//widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js"></script>
		<!-- End TrustBox script -->
		
		<!-- Trustpilot review reuest -->
		<!-- <script>
		        (function(w,d,s,r,n){w.TrustpilotObject=n;w[n]=w[n]||function(){(w[n].q=w[n].q||[]).push(arguments)};
		            a=d.createElement(s);a.async=1;a.src=r;a.type='text/java'+s;f=d.getElementsByTagName(s)[0];
		            f.parentNode.insertBefore(a,f)})(window,document,'script', 'https://invitejs.trustpilot.com/tp.min.js', 'tp');
		            tp('register', 'DNUPyTZG1uKy0dHt');
		</script>
		<!-- End Trustpilot review reuest -->
		
    </head>
    <body @if (BaseHelper::siteLanguageDirection() == 'rtl') dir="rtl" @endif class="@if (BaseHelper::siteLanguageDirection() == 'rtl') rtl @endif header_full_true wowy-template css_scrollbar lazy_icons btnt4_style_2 zoom_tp_2 css_scrollbar template-index wowy_toolbar_true hover_img2 swatch_style_rounded swatch_list_size_small label_style_rounded wrapper_full_width header_full_true header_sticky_true hide_scrolld_true des_header_3 h_banner_true top_bar_true prs_bordered_grid_1 search_pos_canvas lazyload @if (Theme::get('bodyClass')) {{ Theme::get('bodyClass') }} @endif">
        <!-- Google Tag Manager (noscript) -->
			<noscript>
				<iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MPBC629" height="0" width="0" style="display:none;visibility:hidden"></iframe>
			</noscript>
		<!-- End Google Tag Manager (noscript) -->
        {!! apply_filters(THEME_FRONT_BODY, null) !!}
        <div id="alert-container"></div>

        {!! Theme::partial('preloader') !!}

        <header class="header-area header-height-2 {{ $headerStyle }}">
            <div class="header-top header-top-ptb-1 d-none d-lg-block">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-xl-3 col-lg-4">
                            <div class="header-info">
                                <ul>
{{--                                    @if (theme_option('hotline'))--}}
{{--                                        <li><i class="fa fa-phone-alt mr-5"></i><a href="tel:{{ theme_option('hotline') }}">{{ theme_option('hotline') }}</a></li>--}}
{{--                                    @endif--}}

{{--                                    @if (is_plugin_active('ecommerce') && EcommerceHelper::isOrderTrackingEnabled())--}}
{{--                                        <li><i class="far fa-anchor mr-5"></i><a href="{{ route('public.orders.tracking') }}">{{ __('Track Your Order') }}</a></li>--}}
{{--                                    @endif--}}
                                    <!-- TrustBox widget - Micro Combo -->
									<div class="trustpilot-widget" data-locale="it-IT" data-template-id="5419b6ffb0d04a076446a9af" data-businessunit-id="5ba21b21534f320001b0bfb8" data-style-height="20px" data-style-width="100%" data-theme="light" data-font-family="Roboto Condensed">
									  <a href="https://it.trustpilot.com/review/www.milanihome.it" target="_blank" rel="noopener">Trustpilot</a>
									</div>
									<!-- End TrustBox widget -->
                                </ul>
                            </div>
                        </div>

                        <div class="col-xl-5 col-lg-4">
                            <div class="text-center">
                                @if (theme_option('header_messages'))
                                    <div id="news-flash" class="d-inline-block">
                                        <ul>
                                            @foreach(json_decode(theme_option('header_messages'), true) as $headerMessage)
                                                @if (count($headerMessage) == 4)
                                                    <li>
                                                        @if ($headerMessage[0]['value'])
                                                            <i class="{{ $headerMessage[0]['value'] }} d-inline-block mr-5"></i>
                                                        @endif

                                                        @if ($headerMessage[1]['value'])
                                                            <span class="d-inline-block">
                                                                {!! BaseHelper::clean($headerMessage[1]['value']) !!}
                                                            </span>
                                                        @endif
                                                        @if ($headerMessage[2]['value'] && $headerMessage[3]['value'])
                                                            <a class="active d-inline-block" href="{{ url($headerMessage[2]['value']) }}">{!! BaseHelper::clean($headerMessage[3]['value']) !!}</a>
                                                        @endif
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @php $currencies = is_plugin_active('ecommerce') ? get_all_currencies() : []; @endphp

                        @if (is_plugin_active('ecommerce') || is_plugin_active('language'))
                            <div class="col-xl-4 col-lg-4">
                                <div class="header-info header-info-right">
                                        <ul>
                                            <li><a href="https://milanihomehelp.zendesk.com/hc/it" target="_blank">Assistenza</a></li>
                                            @if (is_plugin_active('language'))
                                                {!! Theme::partial('language-switcher') !!}
                                            @endif

                                            @if (is_plugin_active('ecommerce'))
                                                @if (count($currencies) > 1)
                                                    <li>
                                                        <a class="language-dropdown-active" href="#"> <i class="fa fa-coins"></i> {{ get_application_currency()->title }} <i class="fa fa-chevron-down"></i></a>
                                                        <ul class="language-dropdown">
                                                            @foreach ($currencies as $currency)
                                                                @if ($currency->id !== get_application_currency_id())
                                                                    <li><a href="{{ route('public.change-currency', $currency->title) }}">{{ $currency->title }}</a></li>
                                                                @endif
                                                            @endforeach
                                                        </ul>
                                                    </li>
                                                @endif
{{--                                                @if (auth('customer')->check())--}}
{{--                                                    <li><a href="{{ route('customer.overview') }}">{{ auth('customer')->user()->name }}</a></li>--}}
{{--                                                @else--}}
{{--                                                    <li><a href="{{ route('customer.login') }}">{{ __('Log In / Sign Up') }}</a></li>--}}
{{--                                                @endif--}}
                                            @endif
                                        </ul>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @php
                $categoriesWith = array_merge(['slugable', 'activeChildren', 'activeChildren.slugable', 'metadata'], (is_plugin_active('language-advanced') ? ['activeChildren.translations'] : []));
                $categories = !is_plugin_active('ecommerce') ? [] : ProductCategoryHelper::getAllProductCategories()
                    ->where('status', \Botble\Base\Enums\BaseStatusEnum::PUBLISHED)
                    ->where('parent_id', 0)
                    ->loadMissing($categoriesWith);
            @endphp
            <div class="header-middle header-middle-ptb-1 d-none d-lg-block">
                <div class="container">
                    <div class="header-wrap header-space-between">
                        @if (theme_option('logo'))
                            <div class="logo logo-width-1">
                                <a href="{{ route('public.index') }}"><img src="{{ RvMedia::getImageUrl(theme_option('logo')) }}" alt="{{ theme_option('site_title') }}"></a>
                            </div>
                        @endif
                        @if (is_plugin_active('ecommerce'))
                            <div class="search-style-2">
                                <form action="{{ route('public.products') }}" method="get">
                                    <div class="form-group--icon">
                                        {{-- <div class="product-cat-label">{{ __('All Categories') }}</div>
                                        <select class="product-category-select" name="categories[]">
                                            <option value="">{{ __('All Categories') }}</option>
                                            @foreach (ProductCategoryHelper::getProductCategoriesWithIndent() as $category)
                                                <option value="{{ $category->id }}" @if (in_array($category->id, request()->input('categories', []))) selected @endif>{!! $category->indent_text !!} {{ $category->name }}</option>
                                            @endforeach
                                        </select> --}}
                                    </div>
                                    <input type="text" name="q" placeholder="{{ __('Search for items…') }}" autocomplete="off">
                                    <button type="submit"> <i class="far fa-search"></i> </button>
                                </form>
                            </div>
                            <div class="header-action-right">
                                <div class="header-action-2">
                                    @if (EcommerceHelper::isCompareEnabled())
                                        <div class="header-action-icon-2">
                                            <a href="{{ route('public.compare') }}" class="compare-count">
                                                <img class="svgInject" alt="{{ __('Compare') }}" src="{{ Theme::asset()->url('images/icons/icon-compare.svg') }}">
                                                <span class="pro-count blue"><span>{{ Cart::instance('compare')->count() }}</span></span>
                                            </a>
                                        </div>
                                    @endif
                                    @if (EcommerceHelper::isWishlistEnabled())
                                        <div class="header-action-icon-2">
                                            <a href="{{ route('public.wishlist') }}" class="wishlist-count">
                                                <img class="svgInject" alt="{{ __('Wishlist') }}" src="{{ Theme::asset()->url('images/icons/icon-heart.svg') }}">
                                                <span class="pro-count blue">@if (auth('customer')->check())<span>{{ auth('customer')->user()->wishlist()->count() }}</span> @else <span>{{ Cart::instance('wishlist')->count() }}</span>@endif</span>
                                            </a>
                                        </div>
                                    @endif
                                    <div class="header-action-icon-2">
                                        <a class="mini-cart-icon" href="{{ route('public.cart') }}">
                                            <img alt="{{ __('Cart') }}" src="{{ Theme::asset()->url('images/icons/icon-cart.svg') }}">
                                            <span class="pro-count blue">{{ Cart::instance('cart')->count() }}</span>
                                        </a>
                                        <div class="cart-dropdown-wrap cart-dropdown-hm2">
                                            {!! Theme::partial('cart-panel') !!}
                                        </div>
                                    </div>
                                    <div class="header-action-icon-2">
                                        <a href="{{ route('customer.login') }}">
                                            <img alt="{{ __('Sign In') }}" src="{{ Theme::asset()->url('images/icons/icon-user.svg') }}">
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="header-bottom header-bottom-bg-color sticky-bar gray-bg sticky-blue-bg">
                <div class="container">
                    <div class="header-wrap header-space-between position-relative main-nav">
                        @if (theme_option('logo_light'))
                            <div class="logo logo-width-1 d-block d-lg-none">
                                <a href="{{ route('public.index') }}"><img src="{{ RvMedia::getImageUrl(theme_option('logo_light')) }}" alt="{{ theme_option('site_title') }}"></a>
                            </div>
                        @endif

                        @if (theme_option('enabled_browse_categories_on_header', 'yes') == 'yes')
                            @php
                                $openBrowse = $page && $page->template == 'homepage' && $page->getMetaData('expanding_product_categories_on_the_homepage', true) == 'yes';
                                $cantCloseBrowse = $openBrowse && $headerStyle == 'header-style-2';
                            @endphp
                            <div class="main-categories-wrap d-none d-lg-block">
                            <a class="categories-button-active @if ($openBrowse) open @endif @if ($cantCloseBrowse) cant-close @endif" href="#">
                                <span class="fa fa-list"></span> {{ __('Browse Categories') }} <i class="down far fa-chevron-down"></i> <i class="up far fa-chevron-up"></i>
                            </a>
                            <div class="categories-dropdown-wrap categories-dropdown-active-large @if ($openBrowse) default-open open @endif">
                                <ul>
                                    @foreach($categories as $category)
                                        @if ($loop->index < 10)
                                            <li @if ($category->activeChildren->count() > 0) class="has-children" @endif>
                                                <a href="{{ $category->url }}">
                                                    @if ($category->getMetaData('icon_image', true))
                                                        <img src="{{ RvMedia::getImageUrl($category->getMetaData('icon_image', true)) }}" alt="{{ $category->name }}" width="18" height="18">
                                                    @elseif ($category->getMetaData('icon', true))
                                                        <i class="{{ $category->getMetaData('icon', true) }}"></i>
                                                    @endif {{ $category->name }}
                                                </a>

                                                @if ($category->activeChildren->count() > 0)
                                                    <div class="dropdown-menu">
                                                        <ul>
                                                            @foreach($category->activeChildren as $childCategory)
                                                                <li><a class="dropdown-item nav-link nav_item" href="{{ $childCategory->url }}">{{ $childCategory->name }}</a></li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif
                                            </li>
                                        @endif
                                    @endforeach
                                    @if (count($categories) > 10)
                                        <li>
                                            <ul class="more_slide_open">
                                                @foreach($categories as $category)
                                                    @if ($loop->index >= 10)
                                                        <li @if ($category->activeChildren->count() > 0) class="has-children" @endif>
                                                            <a href="{{ $category->url }}">
                                                                @if ($category->getMetaData('icon_image', true))
                                                                    <img src="{{ RvMedia::getImageUrl($category->getMetaData('icon_image', true)) }}" alt="{{ $category->name }}" width="18" height="18">
                                                                @elseif ($category->getMetaData('icon', true))
                                                                    <i class="{{ $category->getMetaData('icon', true) }}"></i>
                                                                @endif {{ $category->name }}
                                                            </a>

                                                            @if ($category->activeChildren->count() > 0)
                                                                <div class="dropdown-menu" style="min-width: 100px;">
                                                                    <ul class="mega-menu d-lg-flex">
                                                                        <li class="mega-menu-col col-lg-7">
                                                                            <ul class="d-lg-flex">
                                                                                <li class="mega-menu-col col-lg-6">
                                                                                    <ul>
                                                                                        @foreach($category->activeChildren as $childCategory)
                                                                                            <li><a class="dropdown-item nav-link nav_item" href="{{ $childCategory->url }}">{{ $childCategory->name }}</a></li>
                                                                                        @endforeach
                                                                                    </ul>
                                                                                </li>
                                                                            </ul>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            @endif
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </li>
                                    @endif
                                </ul>

                                @if (count($categories) > 10)
                                    <div class="more_categories">{{ __('Show more...') }}</div>
                                @endif
                            </div>
                        </div>
                        @endif
                        <div class="main-menu main-menu-padding-1 main-menu-lh-2 d-none d-lg-block main-menu-light-white hover-boder hover-boder-white">
                            <nav>
                                {!!
                                    Menu::renderMenuLocation('main-menu', [
                                        'view' => 'main-menu',
                                    ])
                                !!}
                            </nav>
                        </div>

                        @if (theme_option('hotline'))
                            <div class="hotline d-none d-lg-block">
                                <p><i class="fa fa-phone-alt"></i><span>{{ __('Hotline') }}</span> {{ theme_option('hotline') }}</p>
                            </div>
                        @endif

                        @if (is_plugin_active('ecommerce'))
                            <div class="header-action-right d-block d-lg-none">
                                <div class="header-action-2">
                                    @if (EcommerceHelper::isCompareEnabled())
                                        <div class="header-action-icon-2">
                                            <a href="{{ route('public.compare') }}" class="compare-count">
                                                <img class="svgInject" alt="{{ __('Compare') }}" src="{{ Theme::asset()->url('images/icons/icon-compare-white.svg') }}">
                                                <span class="pro-count white"><span>{{ Cart::instance('compare')->count() }}</span></span>
                                            </a>
                                        </div>
                                    @endif
                                    @if (EcommerceHelper::isWishlistEnabled())
                                        <div class="header-action-icon-2">
                                            <a href="{{ route('public.wishlist') }}" class="wishlist-count">
                                                <img alt="wowy" src="{{ Theme::asset()->url('images/icons/icon-heart-white.svg') }}">
                                                <span class="pro-count white">@if (auth('customer')->check())<span>{{ auth('customer')->user()->wishlist()->count() }}</span> @else <span>{{ Cart::instance('wishlist')->count() }}</span>@endif</span>
                                            </a>
                                        </div>
                                    @endif
                                    <div class="header-action-icon-2">
                                        <a class="mini-cart-icon" href="{{ route('public.cart') }}">
                                            <img alt="cart" src="{{ Theme::asset()->url('images/icons/icon-cart-white.svg') }}">
                                            <span class="pro-count white">{{ Cart::instance('cart')->count() }}</span>
                                        </a>
                                        <div class="cart-dropdown-wrap cart-dropdown-hm2">
                                            {!! Theme::partial('cart-panel') !!}
                                        </div>
                                    </div>
                                    <div class="header-action-icon-2">
                                        <a href="{{ route('customer.login') }}">
                                            <img alt="wowy" src="{{ Theme::asset()->url('images/icons/icon-user-white.svg') }}">
                                        </a>
                                    </div>
                                    <div class="header-action-icon-2 d-block d-lg-none">
                                        <div class="burger-icon burger-icon-white">
                                            <span class="burger-icon-top"></span>
                                            <span class="burger-icon-mid"></span>
                                            <span class="burger-icon-bottom"></span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <div class="mobile-header-active mobile-header-wrapper-style">
            <div class="mobile-header-wrapper-inner">
                <div class="mobile-header-top">
                    @if (theme_option('logo'))
                        <div class="mobile-header-logo">
                            <a href="{{ route('public.index') }}"><img src="{{ RvMedia::getImageUrl(theme_option('logo')) }}" alt="{{ theme_option('site_title') }}"></a>
                        </div>
                    @endif
                    <div class="mobile-menu-close close-style-wrap close-style-position-inherit">
                        <button class="close-style search-close">
                            <i class="icon-top"></i>
                            <i class="icon-bottom"></i>
                        </button>
                    </div>
                </div>
                @if (is_plugin_active('ecommerce'))
                    <div class="mobile-header-content-area">
                    <div class="mobile-search search-style-3 mobile-header-border">
                        <form action="{{ route('public.products') }}">
                            <input type="text" name="q" placeholder="{{ __('Search...') }}">
                            <button type="submit"> <i class="far fa-search"></i> </button>
                        </form>
                    </div>
                    <div class="mobile-menu-wrap mobile-header-border">
                        <div class="main-categories-wrap mobile-header-border">
                            <a class="categories-button-active-2" href="#">
                                <span class="far fa-bars"></span> {{ __('Browse Categories') }} <i class="down far fa-chevron-down"></i>
                            </a>
                            <div class="categories-dropdown-wrap categories-dropdown-active-small">
                                <ul>
                                    @foreach($categories as $category)
                                        <li>
                                            <a href="{{ $category->url }}">
                                                @if ($category->getMetaData('icon_image', true))
                                                    <img src="{{ RvMedia::getImageUrl($category->getMetaData('icon_image', true)) }}" alt="{{ $category->name }}" width="18" height="18">
                                                @elseif ($category->getMetaData('icon', true))
                                                    <i class="{{ $category->getMetaData('icon', true) }}"></i>
                                                @endif {{ $category->name }}

                                                @if ($category->activeChildren->count() > 0)
                                                    <span class="menu-expand"><i class="down far fa-chevron-down"></i></span>
                                                @endif
                                            </a>
                                            @if ($category->activeChildren->count() > 0)
                                                <ul class="dropdown" style="display: none">
                                                    @foreach($category->activeChildren as $childCategory)
                                                        <li><a href="{{ $childCategory->url }}">{{ $childCategory->name }}</a></li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <!-- mobile menu start -->
                        <nav>
                            {!!
                                Menu::renderMenuLocation('main-menu', [
                                    'options' => ['class' => 'mobile-menu'],
                                    'view'    => 'mobile-menu',
                                ])
                            !!}
                        </nav>
                        <!-- mobile menu end -->
                    </div>
                    <div class="mobile-header-info-wrap mobile-header-border">
                        @if (is_plugin_active('language'))
                            <div class="single-mobile-header-info">
                                <a class="mobile-language-active" href="#">{{ __('Language') }} <span><i class="far fa-angle-down"></i></span></a>
                                <div class="lang-curr-dropdown lang-dropdown-active">
                                    <ul>
                                        @php
                                            $showRelated = setting('language_show_default_item_if_current_version_not_existed', true);
                                        @endphp

                                        @foreach (Language::getSupportedLocales() as $localeCode => $properties)
                                            <li><a rel="alternate" hreflang="{{ $localeCode }}" href="{{ $showRelated ? Language::getLocalizedURL($localeCode) : url($localeCode) }}">{!! language_flag($properties['lang_flag'], $properties['lang_name']) !!} {{ $properties['lang_name'] }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                        @if (count($currencies) > 1)
                            <div class="single-mobile-header-info">
                                <a class="mobile-language-active" href="#">{{ __('Currency') }} <span><i class="far fa-angle-down"></i></span></a>
                                <div class="lang-curr-dropdown lang-dropdown-active">
                                    <ul>
                                        @foreach ($currencies as $currency)
                                            <li><a href="{{ route('public.change-currency', $currency->title) }}">{{ $currency->title }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                        @if (is_plugin_active('ecommerce'))
                            <div class="single-mobile-header-info">
                                @if (auth('customer')->check())
                                    <a href="{{ route('customer.overview') }}">{{ auth('customer')->user()->name }}</a>
                                @else
                                    <a href="{{ route('customer.login') }}">{{ __('Log In / Sign Up') }}</a>
                                @endif
                            </div>
                        @endif

                        @if (theme_option('hotline'))
                            <div class="single-mobile-header-info">
                                <a href="tel:{{ theme_option('hotline') }}">{{ theme_option('hotline') }}</a>
                            </div>
                        @endif
                    </div>

                    @if (theme_option('social_links'))
                        <div class="mobile-social-icon">
                            @foreach(json_decode(theme_option('social_links'), true) as $socialLink)
                                @if (count($socialLink) == 4)
                                    <a href="{{ $socialLink[2]['value'] }}"
                                       title="{{ $socialLink[0]['value'] }}" style="background-color: {{ $socialLink[3]['value'] }}; border: 1px solid {{ $socialLink[3]['value'] }};">
                                        <i class="{{ $socialLink[1]['value'] }}"></i>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
