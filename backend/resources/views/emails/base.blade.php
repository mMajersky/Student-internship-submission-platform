<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $subjectLine ?? 'Notification' }}</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f6f6f6; padding: 20px;">
<div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; padding: 30px;">

    <header style="border-bottom: 1px solid #eaeaea; margin-bottom: 20px;">
        <h2 style="color: #007BFF;">SISP Platform</h2>
    </header>

    <main>
        @yield('content')
    </main>

    <footer style="border-top: 1px solid #eaeaea; margin-top: 30px; padding-top: 10px; font-size: 12px; color: #888;">
        <p>© {{ date('Y') }} SISP Platform – Automated Notification</p>
    </footer>

</div>
</body>
</html>
