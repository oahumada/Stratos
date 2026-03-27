@component('mail::message')
# ❌ Approval Rejected

Dear {{ $creator_name }},

We regret to inform you that **{{ $rejector_name }}** has rejected the approval for your scenario **"{{ $scenario_name }}"**.

**Reason for Rejection:**  
{{ $rejection_reason }}

{{ $next_steps }}

@component('mail::button', ['url' => $revise_link, 'color' => 'primary'])
Revise Your Scenario
@endcomponent

---

**Organization**: {{ config('app.name') }}

Thanks,  
{{ config('app.name') }}

@slot('footer')
This is an automated notification from Stratos. Please do not reply to this email.
@endslot
@endcomponent
