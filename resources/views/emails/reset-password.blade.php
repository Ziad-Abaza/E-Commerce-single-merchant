@component('mail::message')
<div style="text-align: center; margin-bottom: 30px;">
    <img src="{{ $logoUrl }}" alt="{{ $siteName }}" style="max-width: 180px; height: auto; border-radius: 10px;">
</div>

# Reset Your Password 🔐

Hi **{{ $user->name }}**,

You requested to reset your password. Click the button below to choose a new one.

---

@component('mail::button', ['url' => $resetUrl, 'color' => 'success'])
Reset Password
@endcomponent

---

### 🔗 Or use this link:
[{{ $resetUrl }}]({{ $resetUrl }})

---

If you did not request a password reset, please ignore this email or contact support.

Best regards,
**The {{ $siteName }} Team**

<div
    style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 12px;">
    <p>© {{ date('Y') }} {{ $siteName }}. All rights reserved.</p>
    <p>This email was sent to {{ $user->email }}</p>
</div>
@endcomponent
