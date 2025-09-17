@component('mail::message')
<div style="text-align: center; margin-bottom: 30px;">
    <img src="{{ $logoUrl }}" alt="{{ $siteName }}" style="max-width: 180px; height: auto; border-radius: 10px;">
</div>

# Welcome to {{ $siteName }} 🎉

Hi **{{ $user->name }}**,
Thank you for joining our store. We’re excited to have you on board!

---

## 🔒 Verify Your Email
To activate your account and start shopping, please click the button below:

@component('mail::button', ['url' => $verificationUrl, 'color' => 'success'])
✅ Verify Your Email
@endcomponent

---

## What’s Next?
Once verified, you’ll be able to:
- 🛒 Browse and shop our products easily.
- 📦 Track your orders and shipments.
- 💬 Contact customer support for assistance.
- 🎁 Enjoy exclusive deals and discounts.

---

## Need Help?
If you have any questions or need assistance, feel free to contact us at **{{ $supportEmail }}**

---

**Note:** If you did not create this account, you can safely ignore this email.

Best regards,
**The {{ $siteName }} Team**

---

@component('mail::subcopy')
If you’re having trouble clicking the "Verify Your Email" button, copy and paste the following URL into your web
browser:
[{{ $verificationUrl }}]({{ $verificationUrl }})
@endcomponent

<div
    style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 12px;">
    <p>© {{ date('Y') }} {{ $siteName }}. All rights reserved.</p>
    <p>This email was sent to {{ $user->email }}</p>
</div>
@endcomponent
