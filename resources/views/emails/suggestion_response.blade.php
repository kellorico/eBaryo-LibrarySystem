@component('mail::message')
# Response to Your Suggestion

Thank you for your feedback!

**Your Suggestion:**
> {{ $suggestion->suggestion }}

**Admin Response:**
{{ $response }}

Thanks for helping us improve eBaryo Library!

@endcomponent 