@extends('plugins/ecommerce::orders.master')
@section('title')
    {{ __('Order successfully. Order number :id', ['id' => $order->code]) }}
@stop
@section('content')

    <div class="container">
        <div class="row">
            <div class="col-lg-7 col-md-6 col-12 left">
                @include('plugins/ecommerce::orders.partials.logo')

                <div class="thank-you">
                    <i class="fa fa-check-circle" aria-hidden="true"></i>
                    <div class="d-inline-block">
                        <h3 class="thank-you-sentence">
                            {{ __('Your order is successfully placed') }}
                        </h3>
                        <p>{{ __('Thank you for purchasing our products!') }}</p>
                    </div>
                </div>

                @include('plugins/ecommerce::orders.thank-you.customer-info', compact('order'))

                <a href="{{ route('public.index') }}" class="btn payment-checkout-btn"> {{ __('Continue shopping') }} </a>
            </div>
            <div class="col-lg-5 col-md-6 d-none d-md-block right">

                @include('plugins/ecommerce::orders.thank-you.order-info')

                <hr>

                @include('plugins/ecommerce::orders.thank-you.total-info', ['order' => $order])
            </div>
        </div>
    </div>
@stop

@push('footer')
<script>
    @php
        $tp_name = $order->address->name;
        $tp_email = $order->address->email;
        $tp_order_code = $order->code;
        $tp_skus = [];
        $tp_products = [];

        foreach($order->products as $product) {
            $tp_skus[] = $product->product->sku;

            $tp_products[] = [
                'sku' => $product->product->sku,
                'productUrl' => $product->product->is_variation ? ($product->product->parentProduct[0]->url . '?s=' . $product->product->sku) : $product->product->url,
                'imageUrl' => asset('storage/' . $product->product_image),
                'name' => $product->product_name,
            ];
        }

        $trustpilot_script = false;
    @endphp

    @if($trustpilot_script)
        document.addEventListener('DOMContentLoaded', function() {
            const trustpilot_invitation = {
                recipientEmail: {!! json_encode($tp_email) !!},
                recipientName: {!! json_encode($tp_name) !!},
                referenceId: {!! json_encode($tp_order_code) !!},
                source: 'InvitationScript',
                productSkus: {!! json_encode($tp_skus) !!},
                products: {!! json_encode($tp_products) !!}
            };

            tp('createInvitation', trustpilot_invitation);
        });
    @endif
</script>    
@endpush