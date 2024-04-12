@if (isset($products) && $products)
    <h5 class="checkout-payment-title">{{ __('Product(s)') }}:</h5>
    @foreach($products as $key => $product)
        @php
            $cartItem = $product->cartItem;
        @endphp

        @if (!empty($product))
            @include('plugins/ecommerce::orders.checkout.product')
        @endif
    @endforeach

    <hr>
@endif
