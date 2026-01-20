<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Temporary Password</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h1 style="color: #1f2937; margin-top: 0;">Student Portal - Temporary Password</h1>
    </div>
    
    <div style="background-color: #ffffff; padding: 20px; border-radius: 5px; border: 1px solid #e5e7eb;">
        <p>Hello {{ $studentName }},</p>
        
        <p>A temporary password has been generated for your student portal account. Please use this password to log in:</p>
        
        <div style="background-color: #fef3c7; border: 2px solid #fbbf24; border-radius: 5px; padding: 15px; margin: 20px 0; text-align: center;">
            <p style="margin: 0; font-size: 18px; font-weight: bold; color: #92400e; font-family: Consolas, Monaco, 'Courier New', monospace; letter-spacing: 0.1em;">
                {{ $temporaryPassword }}
            </p>
        </div>
        
        <p><strong>Important:</strong></p>
        <ul>
            <li>This is a temporary password. Please change it after your first login.</li>
            <li>Keep this password secure and do not share it with anyone.</li>
            <li>If you did not request this password, please contact the administrator immediately.</li>
        </ul>
        
        <p style="margin-top: 30px;">You can log in to the student portal using your student number and this temporary password.</p>
        
        <p style="margin-top: 30px;">Best regards,<br>Student Portal Administration</p>
    </div>
    
    <div style="margin-top: 20px; padding: 15px; background-color: #f3f4f6; border-radius: 5px; font-size: 12px; color: #6b7280; text-align: center;">
        <p style="margin: 0;">This is an automated message. Please do not reply to this email.</p>
    </div>
</body>
</html>

