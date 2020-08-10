@component('mail::message')
# Introduction

Hello <b>{{$user->name}}</b>

Thank you for creating an account. Please verify your email using this button below:
{{route('verify', $user->verification_token)}}

@component('mail::button', ['url' => route('verify', $user->verification_token)])
Verify your account
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
