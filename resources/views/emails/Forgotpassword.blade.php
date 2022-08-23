@component('mail::message')
# Welcome to Letsreason


{{--  Hello <h1>{{ $user['name'] }}</h1>
    <p>{{ $user['info'] }}</p>,  --}}


You requested a password reset on your Flutterwave account.


Please click the button below to set a new password on your account




@component('mail::button', ['url' => 'https://letsreason.co/'])
Verify Account
@endcomponent


Best Regards,<br>
The {{ config('app.name') }} Team
@endcomponent
