# Email Setup Guide for XAMPP

## Option 1: Configure XAMPP to Send Emails via Gmail SMTP

### Step 1: Update php.ini
1. Open XAMPP Control Panel
2. Click "Config" button next to Apache
3. Select "php.ini"
4. Find and update these lines:

```ini
[mail function]
SMTP=smtp.gmail.com
smtp_port=587
sendmail_from=kalungufeeds167@gmail.com
sendmail_path="\"C:\xampp\sendmail\sendmail.exe\" -t"
```

### Step 2: Configure sendmail.ini
1. Navigate to `C:\xampp\sendmail\`
2. Open `sendmail.ini`
3. Update these settings:

```ini
[sendmail]
smtp_server=smtp.gmail.com
smtp_port=587
smtp_ssl=tls
auth_username=kalungufeeds167@gmail.com
auth_password=YOUR_GMAIL_APP_PASSWORD_HERE
force_sender=kalungufeeds167@gmail.com
```

### Step 3: Get Gmail App Password
1. Go to your Google Account settings
2. Security → 2-Step Verification (enable if not already)
3. Security → App passwords
4. Generate a new app password for "Mail"
5. Copy the 16-character password
6. Paste it in `sendmail.ini` as `auth_password`
7. Also update it in `email_config.php`

### Step 4: Restart Apache
1. Stop Apache in XAMPP Control Panel
2. Start Apache again

---

## Option 2: Use PHPMailer Library (Recommended for Production)

If you want to use PHPMailer for more reliable email delivery:

### Step 1: Download PHPMailer
Visit: https://github.com/PHPMailer/PHPMailer/releases
Or run: `composer require phpmailer/phpmailer`

### Step 2: I can help set it up
Just let me know and I'll create the PHPMailer integration.

---

## Testing

After setup, test the contact form:
1. Go to your contact page
2. Fill out the form
3. Submit
4. Check if email arrives at kalungufeeds167@gmail.com

---

## Troubleshooting

**Email not sending?**
- Check if Apache is running
- Verify Gmail credentials are correct
- Make sure 2-step verification is enabled
- Check spam folder
- Review `contact_log.txt` for failed attempts

**Gmail blocking emails?**
- Use App Password (not regular password)
- Enable "Less secure app access" (not recommended)
- Check Gmail security alerts

---

## Current Setup

Your contact form now:
✓ Validates all input
✓ Sends HTML formatted emails
✓ Includes sender info (name, email, phone)
✓ Has error logging as backup
✓ Shows success/error messages to users
✓ Professional email template

Email goes to: **kalungufeeds167@gmail.com**
