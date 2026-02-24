<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | OLGA</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #1da1f2;
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
            --text-main: #ffffff;
            --text-muted: #94a3b8;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: url('/img/login_background.webp') no-repeat center center fixed;
            background-size: cover;
            color: var(--text-main);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        #network-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: 0;
            pointer-events: none;
        }

        .login-card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 420px;
            padding: 40px;
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
            text-align: center;
        }

        .logo { 
            width: 180px; 
            margin-bottom: 25px;
            filter: drop-shadow(0 0 10px rgba(255,255,255,0.2));
        }

        h1 { 
            font-size: 1.8rem; 
            font-weight: 700; 
            margin-bottom: 8px; 
            color: #fff;
        }

        .sub-text {
            color: var(--text-muted);
            font-size: 0.95rem;
            margin-bottom: 30px;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.4);
            font-size: 1rem;
        }

        input {
            width: 100%;
            padding: 14px 20px 14px 50px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 50px;
            color: white;
            font-family: inherit;
            font-size: 0.95rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.25);
            border-color: var(--primary-blue);
            box-shadow: 0 0 15px rgba(29, 161, 242, 0.3);
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: var(--primary-blue);
            border: none;
            border-radius: 50px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
            box-shadow: 0 4px 15px rgba(29, 161, 242, 0.4);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            background: #40b1f5;
            box-shadow: 0 6px 20px rgba(29, 161, 242, 0.6);
        }

        .error-message {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            padding: 12px;
            border-radius: 12px;
            font-size: 0.9rem;
            margin-bottom: 20px;
            text-align: center;
        }

        .footer-text {
            margin-top: 30px;
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.3);
            letter-spacing: 0.5px;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-card { padding: 30px 20px; width: 90%; }
            .logo { width: 150px; }
        }
    </style>
</head>
<body>
    <canvas id="network-overlay"></canvas>

    <div class="login-card">
        <img src="/logo/olga_neu.svg" alt="OLGA Logo" class="logo">
        <h1>Willkommen</h1>
        <p class="sub-text">Logge dich ein, um fortzufahren</p>

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
                <div class="input-wrapper">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="E-Mail Adresse">
                </div>
            </div>

            <div class="form-group">
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" required placeholder="Passwort">
                </div>
            </div>

            <button type="submit" class="btn-submit">Anmelden</button>
        </form>

        <div class="footer-text">
            OLGA - Logistics Gift Assistant &copy; {{ date('Y') }}
        </div>
    </div>

    <script>
        const canvas = document.getElementById('network-overlay');
        const ctx = canvas.getContext('2d');
        let width, height, particles = [];
        
        function resize() {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
            initParticles();
        }

        class Particle {
            constructor() { this.init(); }
            init() {
                this.x = Math.random() * width;
                this.y = Math.random() * height;
                this.vx = (Math.random() - 0.5) * 0.4;
                this.vy = (Math.random() - 0.5) * 0.4;
                this.radius = Math.random() * 1.5 + 0.5;
            }
            update() {
                this.x += this.vx;
                this.y += this.vy;
                if (this.x < 0 || this.x > width) this.vx *= -1;
                if (this.y < 0 || this.y > height) this.vy *= -1;
            }
            draw() {
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
                ctx.fillStyle = 'rgba(255, 255, 255, 0.4)';
                ctx.fill();
            }
        }

        function initParticles() {
            particles = [];
            let count = Math.floor((width * height) / 10000); // Dynamic density
            count = Math.min(Math.max(count, 50), 150);
            for (let i = 0; i < count; i++) {
                particles.push(new Particle());
            }
        }

        function animate() {
            ctx.clearRect(0, 0, width, height);
            
            // Draw connections
            for (let i = 0; i < particles.length; i++) {
                const p = particles[i];
                p.update();
                p.draw();
                
                for (let j = i + 1; j < particles.length; j++) {
                    const p2 = particles[j];
                    const dx = p.x - p2.x;
                    const dy = p.y - p2.y;
                    const dist = Math.sqrt(dx * dx + dy * dy);
                    
                    if (dist < 150) {
                        ctx.beginPath();
                        ctx.strokeStyle = `rgba(255, 255, 255, ${0.12 * (1 - dist / 150)})`;
                        ctx.lineWidth = 0.8;
                        ctx.moveTo(p.x, p.y);
                        ctx.lineTo(p2.x, p2.y);
                        ctx.stroke();
                    }
                }
            }
            requestAnimationFrame(animate);
        }

        window.addEventListener('resize', resize);
        resize();
        animate();
    </script>
</body>
</html>
