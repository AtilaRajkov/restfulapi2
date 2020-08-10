@component('mail::message')
# Please confirm your new email

Hello <b>{{$user->name}}</b>.<br>
You changed your email, so we need to verify this new address. <br>
Please use the button bellow:

@component('mail::button', ['url' => route('verify', $user->verification_token)])
Verify your new email address
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
