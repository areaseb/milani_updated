<li>{{ $address->name }}</li>
@if ($address->phone)
    <li>
        <a href="tel:{{ $address->phone }}">
            <span><i class="fa fa-phone-square cursor-pointer mr5"></i></span>
            <span>{{ $address->phone }}</span>
        </a>
    </li>
@endif
<li>
    @if ($address->email)
        <div><a href="mailto:{{ $address->email }}">{{ $address->email }}</a></div>
    @endif
    @if ($address->address)
        <div>{{ $address->address }}</div>
    @endif
    @if ($address->city)
        <div>
        	@if (EcommerceHelper::isZipCodeEnabled() && $address->zip_code)
		        {{ $address->zip_code }} 
		    @endif
		    {{ $address->city_name }} 
		    @if ($address->state)
		        ({{ $address->state_name }})
		    @endif
		</div>
    @endif    
    @if ($address->country_name)
        <div>{{ $address->country_name }}</div>
    @endif
    @if ($address->vat)
        <div><b>P.IVA / C.F.</b> {{ $address->vat }}</div>
    @endif
    @if ($address->sdi)
        <div><b>SDI</b> {{ $address->sdi }}</div>
    @endif
    @if ($address->pec)
        <div><b>PEC</b> {{ $address->pec }}</div>
    @endif
    
    <div style="text-align: right;">
        <a target="_blank" class="hover-underline" href="https://maps.google.com/?q={{ $address->address }}, {{ $address->city_name }}, {{ $address->state_name }}, {{ $address->country_name }}@if (EcommerceHelper::isZipCodeEnabled()), {{ $address->zip_code }} @endif">{{ trans('plugins/ecommerce::order.see_on_maps') }}</a>
    </div>
</li>
