<x-mail::message>
# New Contact Form Submission

You have received a new inquiry through your website.

**From:** {{ $submission->name }}
**Email:** {{ $submission->email }}
@if($submission->phone)
**Phone:** {{ $submission->phone }}
@endif

@if($property)
**Property Inquiry:** {{ $property->title }}
**Property Address:** {{ $property->address }}, {{ $property->city }}, {{ $property->state }}
@endif

## Message

{{ $submission->message }}

<x-mail::button :url="route('filament.tenant.resources.contact-submissions.view', $submission)">
View in Dashboard
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
