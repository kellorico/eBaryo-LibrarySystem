@component('mail::message')
{{-- Logo/Header --}}
<div style="text-align:center; margin-bottom: 24px;">
    <img src="{{ asset('assets/images/image.png') }}" alt="eBaryo Library" style="max-width: 120px; margin-bottom: 8px;">
    <h2 style="color: #198754; margin: 0;">eBaryo Library Announcement</h2>
</div>

{{-- Custom Intro --}}
@if(!empty($custom_intro))
> {{ $custom_intro }}
@endif

{{-- Announcement Details --}}
# {{ $announcement->title }}

@isset($announcement->type)
**Type:** {{ ucfirst($announcement->type) }}
@endisset
@if($announcement->is_urgent)
<span style="color: #dc3545; font-weight: bold;">URGENT</span>
@endif

{{ $announcement->content }}

@isset($announcement->created_at)
---
<small>Posted on {{ $announcement->created_at->format('F j, Y \a\t g:i A') }}</small>
@endisset

{{-- Call to Action (optional) --}}
@isset($announcement->id)
@component('mail::button', ['url' => url('/')])
Visit eBaryo Library
@endcomponent
@endisset

Thanks,<br>
{{ config('app.name') }} Team
@endcomponent
