{{-- <section class="product-tabs pt-40 pb-30 wow fadeIn animated">
    <product-collections-component title="{!! BaseHelper::clean($title) !!}" :product_collections="{{ json_encode($productCollections) }}" url="{{ route('public.ajax.products') }}"></product-collections-component>
</section> --}}

@if ($productCollections->isNotEmpty())
    <section class="section-padding-60">
        <div class="container wow fadeIn animated">
            @if (clean($productCollections[0]->name))
                <h3 class="section-title style-1 mb-30">{!! BaseHelper::clean($productCollections[0]->name) !!}</h3>
            @endif
            <featured-products-component url="{{ route('public.ajax.products', ['collection_id' => $productCollections[0]->id]) }}"></featured-products-component>
        </div>
    </section>
@endif
