@component('mail::message')
# Welcome to Serenity


Hello **{{$name}}**,


You requested a password reset on your Serenity account.


Please click the button below to set a new password on your account




@component('mail::button', ['url' => 'http://localhost:3000/'])
Verify Account
@endcomponent


Best Regards,<br>
The {{ config('app.name') }} Team
@endcomponent
