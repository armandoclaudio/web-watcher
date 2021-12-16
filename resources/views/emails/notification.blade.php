@component('mail::message')
# New Alerts

@foreach ($records as $record)
@component('mail::button', ['url' => $record['link']])
    {{ $record['title'] }}
@endcomponent
@endforeach
@endcomponent
