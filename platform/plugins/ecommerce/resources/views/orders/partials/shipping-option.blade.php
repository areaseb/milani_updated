@if(in_array(Arr::get($attributes, 'data-option'), ['4', '6']))
	{{-- €9 shipping or free shipping --}}
	@php
		unset($attributes['name']);
		$attributes['checked'] = true;
		$attributes['disabled'] = true;
	@endphp
	<li class="list-group-item">
		{!! Form::checkbox('shipping-method-base', $shippingKey, true, $attributes) !!}
		{{-- {!! Form::checkbox(Arr::get($attributes, 'name'), $shippingKey, Arr::get($attributes, 'checked'), $attributes) !!} --}}
		<label for="{{ Arr::get($attributes, 'id') }}">
	        <div>
	            @if ($image = Arr::get($shippingItem, 'image'))
	                <img src="{{ $image }}" alt="{{ $shippingItem['name'] }}" style="max-height: 40px; max-width: 55px">
	            @endif
	            <span>
	                {{ $shippingItem['name'] }} - 
	                @if ($shippingItem['price'] > 0)
	                    {{ format_price($shippingItem['price']) }}
	                @else
	                    <strong>{{ __('Free shipping') }}</strong>
	                @endif
	            </span>
	        </div>
	        <div>
	            @if ($description = Arr::get($shippingItem, 'description'))
	                <small class="text-secondary">{!! BaseHelper::clean($description) !!}</small>
	            @endif
	            @if ($errorMessage = Arr::get($shippingItem, 'error_message'))
	                <small class="text-danger">{!! BaseHelper::clean($errorMessage) !!}</small>
	            @endif
	        </div>
	    </label>
	</li>
@elseif(Arr::get($attributes, 'data-option') == 5)
	{{-- Contrassegno --}}
	<li class="list-group-item">
		{!! Form::checkbox(Arr::get($attributes, 'name'), $shippingKey, Arr::get($attributes, 'checked'), $attributes) !!}
	    <label for="{{ Arr::get($attributes, 'id') }}">
	        <div>
	            @if ($image = Arr::get($shippingItem, 'image'))
	                <img src="{{ $image }}" alt="{{ $shippingItem['name'] }}" style="max-height: 40px; max-width: 55px">
	            @endif
	            <span>
	                {{ $shippingItem['name'] }} - 
	                @if ($shippingItem['price'] > 0)
	                    {{ format_price($shippingItem['price']) }}
	                @else
	                    <strong>{{ __('Free shipping') }}</strong>
	                @endif
	            </span>
	        </div>
	        <div>
	            @if ($description = Arr::get($shippingItem, 'description'))
	                <small class="text-secondary">{!! BaseHelper::clean($description) !!}</small>
	            @endif
	            @if ($errorMessage = Arr::get($shippingItem, 'error_message'))
	                <small class="text-danger">{!! BaseHelper::clean($errorMessage) !!}</small>
	            @endif
	        </div>
	    </label>
	</li>
@else
	<li class="list-group-item">
	    {!! Form::radio(Arr::get($attributes, 'name'), $shippingKey, Arr::get($attributes, 'checked'), $attributes) !!}
	    <label for="{{ Arr::get($attributes, 'id') }}">
	        <div>
	            @if ($image = Arr::get($shippingItem, 'image'))
	                <img src="{{ $image }}" alt="{{ $shippingItem['name'] }}" style="max-height: 40px; max-width: 55px">
	            @endif
	            <span>
	                {{ $shippingItem['name'] }} - 
	                @if ($shippingItem['price'] > 0)
	                    {{ format_price($shippingItem['price']) }}
	                @else
	                    <strong>{{ __('Free shipping') }}</strong>
	                @endif
	            </span>
	        </div>
	        <div>
	            @if ($description = Arr::get($shippingItem, 'description'))
	                <small class="text-secondary">{!! BaseHelper::clean($description) !!}</small>
	            @endif
	            @if ($errorMessage = Arr::get($shippingItem, 'error_message'))
	                <small class="text-danger">{!! BaseHelper::clean($errorMessage) !!}</small>
	            @endif
	        </div>
	    </label>
	</li>
@endif
