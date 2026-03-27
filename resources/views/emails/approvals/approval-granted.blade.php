@component('mail::message')
# ✅ Approval Granted

Dear {{ $creator_name }},

Great news! **{{ $approver_name }}** has approved your scenario **"{{ $scenario_name }}"**.

{{ $next_steps }}

@component('mail::button', ['url' => config('app.url') . '/scenario-planning/analytics', 'color' => 'success'])
View Your Scenario
@endcomponent

---

**Organization**: {{ config('app.name') }}

Thanks,  
{{ config('app.name') }}

@slot('footer')
This is an automated notification from Stratos. Please do not reply to this email.
@endslot
@endcomponent
