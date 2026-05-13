<x-mail::message>
# New Alerts

@foreach ($records as $record)
<x-mail::button :url="$record['link']">
    {{ $record['title'] }}
</x-mail::button>
@endforeach

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
