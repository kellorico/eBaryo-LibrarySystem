@component('mail::message')
{{-- Logo/Header --}}
<div style="text-align:center; margin-bottom: 24px;">
    <img src="{{ asset('assets/images/image.png') }}" alt="eBaryo Library" style="max-width: 120px; margin-bottom: 8px;">
    <h2 style="color: #198754; margin: 0;">New Reading Challenge!</h2>
</div>

{{-- Custom Intro --}}
@if(!empty($custom_intro))
> {{ $custom_intro }}
@endif

{{-- Challenge Details --}}
# {{ $challenge->title }}

@if(!empty($challenge->description))
{{ $challenge->description }}
@endif

**Target Books:** {{ $challenge->target_books }}

**Start Date:** {{ $challenge->start_date }}

**End Date:** {{ $challenge->end_date }}

@isset($challenge->created_at)
---
<small>Posted on {{ $challenge->created_at->format('F j, Y \a\t g:i A') }}</small>
@endisset

{{-- Call to Action (optional) --}}
@isset($challenge->id)
@component('mail::button', ['url' => url('/')])
Join the Challenge
@endcomponent
@endisset

Thanks,<br>
{{ config('app.name') }} Team
@endcomponent 