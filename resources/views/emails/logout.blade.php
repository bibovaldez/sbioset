<p>Dear {{ Auth::user()->name }},</p>

<p>{{ __('You have logged into multiple sessions. If you want to log out from all other sessions, click the link below:') }}
</p>

<p><a href="{{ $logoutLink }}">Logout from other sessions</a></p>

<p>{{ __('If you did not expect to receive this email, please ignore it.') }}</p>

{{ __('Regards,') }}<br>
{{ config('app.name') }}

{{ __('If you’re having trouble clicking the "Logout from other sessions" button, copy and paste the URL below into your web browser:') }}
[{{ $logoutLink }}]({{ $logoutLink }})
