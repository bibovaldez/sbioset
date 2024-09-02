<p>Dear {{ Auth::user()->name }},</p>

<p>You have logged into multiple sessions. If you want to log out from all other sessions, click the link below:</p>

<p><a href="{{ $logoutLink }}">Logout from other sessions</a></p>

<p>If you did not perform this action, please ignore this email.</p>

<p>Regards,<br>Bioset</p>
