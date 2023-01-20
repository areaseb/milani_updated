<div class="page-header breadcrumb-wrap">
    <div class="container">
        <div class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
            @foreach ($crumbs = Theme::breadcrumb()->getCrumbs() as $i => $crumb)
                @if ($i != (count($crumbs) - 1))
                    <div itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="breadcrumb-item d-inline-block">
                        <a href="{{ $crumb['url'] }}" itemprop="item" title="{{ $crumb['label'] }}">
                            {{ $crumb['label'] }}
                            <meta itemprop="name" content="{{ $crumb['label'] }}" />
                        </a>
                        <meta itemprop="position" content="{{ $i + 1}}" />
                    </div>
                    <span></span>
                @else
                    <div class="breadcrumb-item d-inline-block active" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <span itemprop="item">
                            {!! $crumb['label'] !!}
                        </span>
                        <meta itemprop="name" content="{{ $crumb['label'] }}" />
                        <meta itemprop="position" content="{{ $i + 1}}" />
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>
