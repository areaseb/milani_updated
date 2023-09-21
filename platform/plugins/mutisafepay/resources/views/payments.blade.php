<ul>
    @foreach($payments->payments as $payment)
        <li>
            @include('plugins/multisafepay::detail', compact('payment'))
        </li>
    @endforeach
</ul>
