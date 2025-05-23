<p>Hi {{ $application->tenant->first_name ?? $application->tenant->name }},</p>
<p>Your application for <strong>{{ $application->pad->padName }}</strong> has been <span style="color:green;font-weight:bold;">approved</span>!</p>
<p>You may now contact the landlord or check your dashboard for further instructions.</p>
<p>Thank you for using FindMyPad!</p> 