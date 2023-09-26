<ul>
    @foreach($payments->payments as $payment)
        <li>
            @include('plugins/klarna::detail', compact('payment'))
        </li>
    @endforeach
</ul>
