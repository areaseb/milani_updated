<x-mail::message>
# Importazione prodotti fallita

{{ $message }}

<x-mail::table>
| N. ROW       | ATTRIBUTE    | ERRORS |
|-----------|--------------|--------|
@foreach ($data['failures'] as $row)
| {{ $row->row() }} | {{ $row->attribute() }} | {{ implode(', ', $row->errors()) }} |
@endforeach
</x-mail::table>

Grazie,<br>
{{ config('app.name') }}
</x-mail::message>
