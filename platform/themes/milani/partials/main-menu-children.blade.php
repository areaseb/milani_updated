<div class="sub-menu" x-data="{ main: 0, sub: -1 }">
    <div class="first-col">
        <div>
            <ul>
                @php
                    $index = 0;
                @endphp
                @foreach ($menu_nodes as $key => $row)
                    <li x-on:mouseover="main = {{ $index }}, sub = -1" x-bind:class="{ 'active': main == {{ $index }}}">
                        <a href="{{ url($row->url) }}" @if ($row->active) class="active" @endif target="{{ $row->target }}">
                            {{ $row->title }}
                            @if ($row->has_child)
                                @if ($row->parent_id) <i class="fa fa-chevron-right"></i> @else <i class="fa fa-chevron-down"></i> @endif
                            @endif
                        </a>
                    </li>

                    @php
                        $index++;
                    @endphp
                @endforeach
            </ul>

            @php
                $index = 0;
            @endphp
            @foreach ($menu_nodes as $key => $row)
                @if ($row->has_child)
                    <ul x-show="main == {{ $index }}" class="sub-sub-menu">
                        @php
                            $sub = 0;
                        @endphp
                        @foreach ($row->child as $key => $row)
                            <li x-on:mouseenter="sub = {{ $sub }}" x-on:mouseleave="sub = -1">
                                <a href="{{ url($row->url) }}" @if ($row->active) class="active" @endif target="{{ $row->target }}">
                                    {{ $row->title }}
                                </a>
                            </li>

                            @php
                                $sub++;
                            @endphp
                        @endforeach
                    </ul>
                @endif

                @php
                    $index++;
                @endphp
            @endforeach
        </div>
    </div>

    <div class="second-col">
        @php
            $index = 0;
        @endphp
        @foreach ($menu_nodes as $key => $row)
            <a href="{{ url($row->url) }}" @if ($row->active) class="active" @endif target="{{ $row->target }}" x-show="main == {{ $index }} && sub == -1">
                @if ($iconImage = $row->getMetadata('icon_image', true))
                    <img src="{{ RvMedia::getImageUrl($iconImage) }}" alt="{{ $row->title }}"/>
                @endif
            </a>

            @if ($row->has_child)
                @php
                    $sub = 0;
                @endphp
                @foreach ($row->child as $key => $row)
                    <a href="{{ url($row->url) }}" @if ($row->active) class="active" @endif target="{{ $row->target }}" x-show="main == {{ $index }} && sub == {{ $sub }}">
                        @if ($iconImage = $row->getMetadata('icon_image', true))
                            <img src="{{ RvMedia::getImageUrl($iconImage) }}" alt="{{ $row->title }}"/>
                        @endif
                    </a>

                    @php
                        $sub++;
                    @endphp
                @endforeach
            @endif

            @php
                $index++;
            @endphp
        @endforeach
    </div>
</div>
