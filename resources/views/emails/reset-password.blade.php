@component('mail::message')
<div style="text-align: center; margin-bottom: 30px;">
    <img src="{{ $logoUrl }}" alt="{{ $siteName }}" style="max-width: 180px; height: auto; border-radius: 10px;">
</div>

# Reset Your Password 🔐

Hi **{{ $user->name ?? 'there' }}**,

You're receiving this email because we received a password reset request for your **{{ $siteName }}** account.

---

## 🔄 Reset Your Password

Click the button below to reset your password. This link will expire in 60 minutes.

@component('mail::button', ['url' => $url, 'color' => 'warning'])
Reset Password
@endcomponent

---

## Security Notice

If you did not request a password reset, no further action is required. Your account remains secure.
**Important:** Never share this link with anyone. {{ $siteName }} staff will never ask for your password or this reset
link.

---

## Need Help?

If you’re having trouble clicking the button above, copy and paste the following URL into your web browser:

[{{ $url }}]({{ $url }})

For additional support, contact us at **{{ $supportEmail }}**.

---

Best regards,
**The {{ $siteName }} Security Team**

---

<div
    style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 12px;">
    <p>© {{ date('Y') }} {{ $siteName }}. All rights reserved.</p>
    <p>This password reset link will expire in 60 minutes.</p>
</div>
@endcomponent
