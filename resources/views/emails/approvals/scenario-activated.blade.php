@component('mail::message')
# 🚀 Scenario Activated

Dear {{ $stakeholder_name }},

Exciting news! The scenario **"{{ $scenario_name }}"** has been approved and activated for execution.

**Execution Details:**
- **Number of Phases**: {{ $phases_count }}
- **Timeline**: {{ $timeline_weeks }} weeks
- **Status**: Now in active execution phase

You can track the progress and milestones below:

@component('mail::button', ['url' => $tracking_link, 'color' => 'success'])
Track Execution Progress
@endcomponent

---

**Organization**: {{ config('app.name') }}

Thanks,  
{{ config('app.name') }}

@slot('footer')
This is an automated notification from Stratos. Please do not reply to this email.
@endslot
@endcomponent
