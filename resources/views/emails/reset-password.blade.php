<!DOCTYPE html>
<html>
<body>
<h2>Password Reset Request</h2>

<p>You requested to reset your password.</p>

<p>Click the link below to reset:</p>

<a href="{{ url('/reset-password?token=' . $token . '&email=' . $email) }}">
    Reset Password
</a>

<p>If you didn’t request this, ignore this email.</p>
</body>
</html>
