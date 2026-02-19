@component('mail::message')

# Reset Your Password

We received a request to reset your password.

Your OTP Code is:

# {{ $otp }}

@component('mail::button', ['url' => url('/api/reset-password')])
Reset Password
@endcomponent

If you did not request a password reset, no further action is required.

Thanks,<br>
{{ config('app.name') }}

@endcomponent
