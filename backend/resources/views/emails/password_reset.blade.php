@extends('emails.base')

@section('content')
    <h2 style="color: #333; margin-bottom: 20px;">Obnovenie hesla / Password Reset</h2>
    
    <div style="margin-bottom: 30px;">
        <p style="color: #333; font-size: 15px; line-height: 1.6;">
            <strong>SK:</strong> Obdržali sme žiadosť o obnovenie hesla pre váš účet v systéme SISP.
        </p>
        <p style="color: #666; font-size: 14px; line-height: 1.6;">
            <strong>EN:</strong> We received a request to reset the password for your account in the SISP system.
        </p>
    </div>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $resetUrl }}" 
           style="display: inline-block; 
                  padding: 14px 30px; 
                  background-color: #007BFF; 
                  color: #ffffff; 
                  text-decoration: none; 
                  border-radius: 6px; 
                  font-weight: bold;
                  font-size: 16px;">
            Obnoviť heslo / Reset Password
        </a>
    </div>
    
    <div style="margin-bottom: 20px;">
        <p style="color: #333; font-size: 14px; line-height: 1.6;">
            <strong>SK:</strong> Tento odkaz je platný {{ $expirationMinutes ?? 60 }} minút. Po uplynutí tejto doby budete musieť požiadať o nový odkaz.
        </p>
        <p style="color: #666; font-size: 13px; line-height: 1.6;">
            <strong>EN:</strong> This link is valid for {{ $expirationMinutes ?? 60 }} minutes. After this time, you will need to request a new link.
        </p>
    </div>
    
    <div style="background-color: #fff3cd; border: 1px solid #ffc107; border-radius: 6px; padding: 15px; margin: 20px 0;">
        <p style="color: #856404; font-size: 13px; margin: 0; line-height: 1.5;">
            <strong>⚠️ SK:</strong> Ak ste túto žiadosť nepodali vy, môžete tento e-mail ignorovať. Vaše heslo zostane nezmenené.
        </p>
        <p style="color: #856404; font-size: 12px; margin: 10px 0 0 0; line-height: 1.5;">
            <strong>⚠️ EN:</strong> If you did not request this, you can ignore this email. Your password will remain unchanged.
        </p>
    </div>
    
    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eaeaea;">
        <p style="color: #999; font-size: 12px; margin-bottom: 10px;">
            <strong>SK:</strong> Ak tlačidlo nefunguje, skopírujte a vložte nasledujúci odkaz do vášho prehliadača:
        </p>
        <p style="color: #999; font-size: 11px; margin-bottom: 5px;">
            <strong>EN:</strong> If the button doesn't work, copy and paste the following link into your browser:
        </p>
        <p style="word-break: break-all; font-size: 12px; color: #007BFF;">
            {{ $resetUrl }}
        </p>
    </div>
@endsection
