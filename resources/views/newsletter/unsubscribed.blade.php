<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Unsubscribed — {{ config('app.name') }}</title>
    <style>
        :root { color-scheme: light dark; }
        * { box-sizing: border-box; }
        body {
            margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center;
            font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif;
            background: #0f172a; color: #f8fafc; padding: 24px;
        }
        .card {
            max-width: 440px; width: 100%; text-align: center;
            background: #141e36; border: 1px solid #1e293b; border-radius: 16px; padding: 40px 32px;
        }
        .mark { color: #22c55e; font-weight: 800; font-size: 20px; letter-spacing: -0.02em; margin-bottom: 24px; }
        .check {
            width: 56px; height: 56px; border-radius: 999px; background: rgba(34,197,94,0.15);
            display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;
            color: #22c55e; font-size: 28px;
        }
        h1 { font-size: 20px; margin: 0 0 10px; }
        p { color: #94a3b8; font-size: 15px; line-height: 1.6; margin: 0 0 8px; }
        .email { color: #e2e8f0; font-weight: 600; }
        a.btn {
            display: inline-block; margin-top: 24px; padding: 11px 22px; border-radius: 10px;
            background: #16a34a; color: #fff; text-decoration: none; font-weight: 600; font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="mark">AfricanMRR</div>
        <div class="check">&check;</div>
        <h1>You're unsubscribed</h1>
        <p><span class="email">{{ $email }}</span> won't receive the weekly report anymore.</p>
        <p>Changed your mind? You can always re-subscribe from the site.</p>
        <a class="btn" href="{{ config('app.url') }}">Back to AfricanMRR</a>
    </div>
</body>
</html>
