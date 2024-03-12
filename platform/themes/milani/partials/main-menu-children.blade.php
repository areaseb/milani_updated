<div class="sub-menu" x-data="{ item: 0 }">
    <div class="first-col">
        <div>
            <ul>
                @php
                    $index = 0;
                @endphp
                @foreach ($menu_nodes as $key => $row)
                    <li>
                        <a href="{{ url($row->url) }}" @if ($row->active) class="active" @endif target="{{ $row->target }}" x-on:mouseover="item = {{ $index }}" x-bind:class="{ 'active': item == {{ $index }}}">
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
                    <ul x-show="item == {{ $index }}" class="sub-sub-menu">
                        @foreach ($row->child as $key => $row)
                            <li>
                                <a href="{{ url($row->url) }}" @if ($row->active) class="active" @endif target="{{ $row->target }}">
                                    {{ $row->title }}
                                </a>
                            </li>
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
            <a href="{{ url($row->url) }}" @if ($row->active) class="active" @endif target="{{ $row->target }}" x-show="item == {{ $index }}">
                @if ($iconImage = $row->getMetadata('icon_image', true))
                    <img src="{{ RvMedia::getImageUrl($iconImage) }}" alt="{{ $row->title }}"/>
                @endif
            </a>
            @php
                $index++;
            @endphp
        @endforeach
    </div>
</div>
