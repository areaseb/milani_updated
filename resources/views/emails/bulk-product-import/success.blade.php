<x-mail::message>
# Importazione completata con successo

{{ $message }}

@if (!empty($data['failures']))
Prodotti che hanno generato errori:

<x-mail::table>
| N. ROW      | ATTRIBUTE    | ERRORS |
|-----------|--------------|--------|
@foreach ($data['failures'] as $row)
| {{ $row->row() }} | {{ $row->attribute() }} | {{ implode(', ', $row->errors()) }} |
@endforeach
</x-mail::table>
@endif

Grazie,<br>
{{ config('app.name') }}
</x-mail::message>
