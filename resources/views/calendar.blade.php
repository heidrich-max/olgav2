<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>OLGA - Kalender</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-accent: #1DA1F2;
            --glass-bg: rgba(255, 255, 255, 0.12);
            --glass-border: rgba(255, 255, 255, 0.2);
            --text-main: #ffffff;
            --text-muted: #cbd5e1;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: url('/img/login_background.webp') no-repeat center center fixed;
            background-size: cover;
            color: var(--text-main);
            min-height: 100vh;
            overflow-x: hidden;
        }

        #network-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: 0;
            pointer-events: none;
        }

        .navbar {
            position: sticky; top: 0; z-index: 100;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(15px);
            padding: 12px 40px;
            display: flex; justify-content: space-between; align-items: center;
            border-bottom: 1px solid var(--glass-border);
            box-shadow: 0 4px 20px rgba(0,0,0,0.4);
        }
        .navbar img { height: 38px; }

        .container { position: relative; z-index: 10; padding: 40px; max-width: 1400px; margin: 0 auto; }

        .card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px; padding: 25px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5);
        }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--glass-border); padding-bottom: 10px; }
        .card h2 { font-size: 1.1rem; font-weight: 600; display: flex; align-items: center; gap: 10px; }

        .btn-back {
            color: var(--text-muted); text-decoration: none; display: flex; align-items: center; gap: 8px; font-size: 0.9rem; transition: color 0.3s;
        }
        .btn-back:hover { color: var(--primary-accent); }

        /* FullCalendar Glass Style */
        .fc {
            --fc-border-color: rgba(255, 255, 255, 0.1);
            --fc-button-bg-color: rgba(255, 255, 255, 0.05);
            --fc-button-border-color: rgba(255, 255, 255, 0.1);
            --fc-button-hover-bg-color: rgba(255, 255, 255, 0.1);
            --fc-button-active-bg-color: var(--primary-accent);
            --fc-page-bg-color: transparent;
            --fc-neutral-bg-color: rgba(255, 255, 255, 0.02);
            --fc-list-event-hover-bg-color: rgba(255, 255, 255, 0.05);
            color: #fff;
        }
        .fc .fc-toolbar-title { font-size: 1.2rem; font-weight: 600; }
        .fc .fc-button { border-radius: 8px; text-transform: capitalize; padding: 6px 12px; font-size: 0.9rem; }
        .fc .fc-daygrid-day.fc-day-today { background: rgba(29, 161, 242, 0.05) !important; }
        .fc .fc-event { border-radius: 4px; padding: 2px 4px; border: none !important; box-shadow: 0 2px 4px rgba(0,0,0,0.2); }
        .fc-theme-standard td, .fc-theme-standard th { border-color: rgba(255,255,255,0.08); }
        .fc-list { background: rgba(255,255,255,0.02); border-radius: 12px; }
    </style>
</head>
<body>
    <canvas id="network-overlay"></canvas>

    <nav class="navbar">
        <div style="display: flex; align-items: center; gap: 30px;">
            <img src="/logo/olga_neu.svg" alt="Frank Group">
            <a href="{{ route('my.dashboard') }}" class="btn-back"><i class="fas fa-chevron-left"></i> Zurück zum Dashboard</a>
        </div>
        <div style="display: flex; align-items: center; gap: 20px;">
            <a href="https://calendar.google.com" target="_blank" class="status-pill" style="text-decoration: none; background: var(--primary-accent); color: #fff; padding: 6px 15px; font-weight: 600;">
                <i class="fas fa-plus"></i> Termin in Google erstellen
            </a>
            <span style="font-size: 0.9rem; font-weight: 500;">{{ $user->name_komplett }}</span>
        </div>
    </nav>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-calendar-alt"></i> Firmenkalender (info@frank.group)</h2>
            </div>
            <div id="calendar" style="min-height: 700px;"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var events = {!! $eventsJson !!};

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'de',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listMonth'
                },
                buttonText: { today: 'Heute', month: 'Monat', week: 'Woche', list: 'Liste' },
                events: events,
                firstDay: 1,
                height: 'auto',
                eventClick: function(info) {
                    alert('Termin: ' + info.event.title + (info.event.extendedProps.location ? '\nOrt: ' + info.event.extendedProps.location : ''));
                },
                eventTextColor: '#fff',
                eventBackgroundColor: 'rgba(29, 161, 242, 0.6)',
                eventBorderColor: 'transparent'
            });
            calendar.render();
        });

        // Simple Background Animation
        const canvas = document.getElementById('network-overlay');
        const ctx = canvas.getContext('2d');
        let width, height, particles = [];
        function resize() { width = canvas.width = window.innerWidth; height = canvas.height = window.innerHeight; initParticles(); }
        class Particle {
            constructor() { this.init(); }
            init() { this.x = Math.random() * width; this.y = Math.random() * height; this.vx = (Math.random() - 0.5) * 0.3; this.vy = (Math.random() - 0.5) * 0.3; this.radius = 1.2; }
            update() { this.x += this.vx; this.y += this.vy; if (this.x < 0 || this.x > width) this.vx *= -1; if (this.y < 0 || this.y > height) this.vy *= -1; }
            draw() { ctx.beginPath(); ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2); ctx.fillStyle = 'rgba(255, 255, 255, 0.3)'; ctx.fill(); }
        }
        function initParticles() { particles = []; for (let i = 0; i < 60; i++) particles.push(new Particle()); }
        function animate() {
            ctx.clearRect(0, 0, width, height);
            particles.forEach((p, i) => {
                p.update(); p.draw();
                for (let j = i + 1; j < particles.length; j++) {
                    const p2 = particles[j];
                    const dx = p.x - p2.x; const dy = p.y - p2.y;
                    const dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < 150) {
                        ctx.beginPath(); ctx.strokeStyle = `rgba(255, 255, 255, ${0.1 * (1 - dist / 150)})`;
                        ctx.moveTo(p.x, p.y); ctx.lineTo(p2.x, p2.y); ctx.stroke();
                    }
                }
            });
            requestAnimationFrame(animate);
        }
        window.addEventListener('resize', resize);
        resize();
        animate();
    </script>
</body>
</html>
