@component('mail::message')
# ✉️ Action Required: Approval Request

Dear {{ $approver_name }},

**{{ $submitted_by }}** has submitted the scenario **"{{ $scenario_name }}"** for your approval.

Please review and provide your feedback at your earliest convenience.

@component('mail::button', ['url' => $approval_link, 'color' => 'success'])
Review & Approve
@endcomponent

If you would like to reject this scenario, you can do so here:

@component('mail::button', ['url' => $rejection_link, 'color' => 'error'])
Reject & Provide Feedback
@endcomponent

---

**Organization**: {{ $organization_name }}

**What is a Scenario?**  
Scenarios are strategic workforce plans that outline potential organizational changes, including headcount adjustments, role restructuring, and talent movements over a defined time horizon.

Thanks,  
{{ config('app.name') }}

@slot('footer')
This is an automated notification from Stratos. Please do not reply to this email.
@endslot
@endcomponent
