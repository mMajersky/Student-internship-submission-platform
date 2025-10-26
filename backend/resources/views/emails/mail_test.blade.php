@extends('emails.base')

@section('content')
    <h3>Hello {{ $studentName ?? 'User' }},</h3>

    <p>This is a test email from <strong>SISP Platform</strong>.</p>

    <p>Your internship <strong>{{ $internshipTitle ?? 'Example Internship' }}</strong> has been approved!</p>

    <a href="{{ $actionUrl ?? '#' }}"
       style="display:inline-block; margin-top:20px; background:#007BFF; color:#fff; text-decoration:none; padding:10px 20px; border-radius:4px;">
        View Details
    </a>

    <p style="margin-top:30px;">Best regards,<br>The SISP Team</p>
@endsection
