Hello {{$user->name}}

Thank you for creating an acccount. Please vefiry email using this link:

{{route('verify',$user->verification_token)}}