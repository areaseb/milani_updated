<div @class(['mb-3', 'widget-item', 'col-md-' . $columns => $columns]) id="{{ $id . '-parent' }}">
    <div class="h-100 bg-{{ $color }}-opacity position-relative">
        <div class="d-flex px-2 py-3 position-relative">
            @if($icon)
                <div class="block-left d-flex mr-1">
            <span class="align-self-center bg-white p-1">
                <i class="{{ $icon }} fa-2x m-2"></i>
            </span>
                </div>
            @endif
            <div class="block-content mx-3">
                <p class="mb-1">{{ $label }}</p>
                <h5>{{ $value }}</h5>
            </div>
        </div>
        @if($hasChart)
            <div id="{{ $id }}" class="position-absolute fixed-bottom"></div>
        @endif
    </div>

    @if($hasChart)
        @include('core/base::widgets.partials.chart-script')
    @endif
</div>
