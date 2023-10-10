<x-mail::message>
# Problema importazione ordine BeezUP
<br>
Il cliente non ha un identificativo valido.<br>
<br>
Ordine ID: {{ $orderId }}<br>

<x-mail::button :url="$orderUrl">
Vedi ordine su Beezup
</x-mail::button>

Grazie,<br>
{{ config('app.name') }}
</x-mail::message>
