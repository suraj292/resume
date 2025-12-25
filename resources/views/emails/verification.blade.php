<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f8fafc;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            padding: 40px 30px;
            text-align: center;
        }
        .logo {
            display: inline-block;
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            line-height: 48px;
            font-size: 24px;
            color: white;
            margin-bottom: 16px;
        }
        .header h1 {
            margin: 0;
            color: white;
            font-size: 24px;
            font-weight: 700;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 16px;
        }
        .message {
            font-size: 15px;
            line-height: 1.6;
            color: #64748b;
            margin-bottom: 32px;
        }
        .button-container {
            text-align: center;
            margin: 32px 0;
        }
        .button {
            display: inline-block;
            padding: 14px 32px;
            background: #4f46e5;
            color: white !important;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 15px;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.3);
        }
        .button:hover {
            background: #4338ca;
        }
        .expiry-notice {
            font-size: 13px;
            color: #94a3b8;
            text-align: center;
            margin-top: 24px;
        }
        .footer {
            background: #f8fafc;
            padding: 24px 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        .footer-text {
            font-size: 13px;
            color: #94a3b8;
            margin: 0;
        }
        .footer-link {
            color: #4f46e5;
            text-decoration: none;
        }
        .divider {
            height: 1px;
            background: #e2e8f0;
            margin: 32px 0;
        }
        .alternative-link {
            font-size: 12px;
            color: #94a3b8;
            word-break: break-all;
            padding: 16px;
            background: #f8fafc;
            border-radius: 8px;
            margin-top: 24px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">📄</div>
            <h1>ResumeAI</h1>
        </div>
        
        <div class="content">
            <div class="greeting">Hello {{ $user->name }}!</div>
            
            <div class="message">
                Welcome to ResumeAI! We're excited to have you on board. To get started with creating professional, ATS-friendly resumes, please verify your email address by clicking the button below.
            </div>
            
            <div class="button-container">
                <a href="{{ $verificationUrl }}" class="button">
                    Verify Email Address
                </a>
            </div>
            
            <div class="expiry-notice">
                This verification link will expire in 60 minutes.
            </div>
            
            <div class="divider"></div>
            
            <div class="message">
                If you didn't create an account with ResumeAI, you can safely ignore this email.
            </div>
            
            <div class="alternative-link">
                <strong>Having trouble with the button?</strong><br>
                Copy and paste this URL into your browser:<br>
                <a href="{{ $verificationUrl }}" style="color: #4f46e5;">{{ $verificationUrl }}</a>
            </div>
        </div>
        
        <div class="footer">
            <p class="footer-text">
                © {{ date('Y') }} ResumeAI. All rights reserved.<br>
                <a href="{{ config('app.frontend_url') }}" class="footer-link">Visit our website</a>
            </p>
        </div>
    </div>
</body>
</html>
