<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Frankgroup Modernisierung</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #007aff;
            --bg-color: #0f172a;
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        body {
            margin: 0; padding: 0;
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            height: 100vh;
            display: flex; align-items: center; justify-content: center;
            overflow: hidden; color: white;
        }

        .background-blobs { position: absolute; width: 100%; height: 100%; z-index: 0; overflow: hidden; }
        .blob {
            position: absolute; width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(0, 122, 255, 0.15) 0%, transparent 70%);
            border-radius: 50%; filter: blur(50px); animation: move 20s infinite alternate;
        }
        .blob-1 { top: -100px; left: -100px; animation-delay: 0s; }
        .blob-2 { bottom: -100px; right: -100px; background: radial-gradient(circle, rgba(236, 72, 153, 0.1) 0%, transparent 70%); animation-delay: -5s; }

        @keyframes move {
            from { transform: translate(0, 0) scale(1); }
            to { transform: translate(100px, 50px) scale(1.1); }
        }

        .login-card {
            position: relative; z-index: 10;
            width: 100%; max-width: 420px; padding: 40px;
            background: var(--glass-bg); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border); border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .logo { display: block; margin: 0 auto 30px; width: 200px; filter: drop-shadow(0 0 10px rgba(255,255,255,0.2)); }
        h1 { font-size: 24px; font-weight: 600; text-align: center; margin-bottom: 30px; letter-spacing: -0.5px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 14px; font-weight: 300; margin-bottom: 8px; color: rgba(255, 255, 255, 0.6); }
        input {
            width: 100%; padding: 14px 16px; background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px;
            color: white; font-family: inherit; font-size: 16px; box-sizing: border-box; transition: all 0.3s;
        }
        input:focus { outline: none; border-color: var(--primary); background: rgba(255, 255, 255, 0.08); box-shadow: 0 0 0 4px rgba(0, 122, 255, 0.2); }
        .btn-submit {
            width: 100%; padding: 14px; background: var(--primary); border: none; border-radius: 12px;
            color: white; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s; margin-top: 10px;
        }
        .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 10px 20px -10px rgba(0, 122, 255, 0.5); background: #0084ff; }
        .error-message { background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; padding: 12px; border-radius: 12px; font-size: 14px; margin-bottom: 20px; text-align: center; }
        .footer-text { text-align: center; font-size: 13px; color: rgba(255, 255, 255, 0.4); margin-top: 30px; }
    </style>
</head>
<body>
    <div class="background-blobs">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
    </div>

    <div class="login-card">
        <img src="https://cms.frankgroup.net/logo/logo-olga.svg" alt="Frankgroup Logo" class="logo">
        <h1>Anmeldung</h1>

        @if ($errors->any())
            <div class="error-message">
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="email">E-Mail Adresse</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="name@beispiel.de">
            </div>

            <div class="form-group">
                <label for="password">Passwort</label>
                <input type="password" id="password" name="password" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn-submit">Jetzt einloggen</button>
        </form>

        <div class="footer-text">
            &copy; {{ date('Y') }} Frankgroup Modernisierungsprojekt
        </div>
    </div>
</body>
</html>
