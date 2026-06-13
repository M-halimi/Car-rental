<x-mail::message>
# Welcome to CarRental.ma

Your agency **{{ $agencyName }}** has been created successfully.

## Login Credentials

- **Dashboard URL:** [{{ $dashboardUrl }}]({{ $dashboardUrl }})
- **Email:** {{ $email }}
- **Password:** {{ $password }}

<x-mail::button :url="$dashboardUrl">
Go to Dashboard
</x-mail::button>

Please change your password after logging in.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
