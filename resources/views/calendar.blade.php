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

        .todo-badge {
            background: #ef4444; color: white; font-size: 0.65rem; font-weight: 700;
            padding: 2px 6px; border-radius: 50px; margin-left: 5px;
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 18px; height: 18px; vertical-align: middle;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
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
            <a href="javascript:void(0)" onclick="openEventModal()" class="status-pill" style="text-decoration: none; background: var(--primary-accent); color: #fff; padding: 6px 15px; font-weight: 600;">
                <i class="fas fa-plus"></i> Termin direkt erstellen
            </a>
            <a href="https://calendar.google.com" target="_blank" style="color: var(--text-muted); text-decoration: none; font-size: 0.9rem;">
                <i class="fas fa-external-link-alt"></i> Zu Google
            </a>
            <span id="navUserName" style="font-size: 0.9rem; font-weight: 500;">{{ $user->name_komplett }}</span>
            @if(isset($openTodoCount) && $openTodoCount > 0)
                <span class="todo-badge" id="navTodoBadge">{{ $openTodoCount }}</span>
            @endif
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
                    let msg = 'Termin: ' + info.event.title;
                    if (info.event.extendedProps.location) msg += '\nOrt: ' + info.event.extendedProps.location;
                    if (info.event.extendedProps.description) msg += '\n\nBeschreibung:\n' + info.event.extendedProps.description;
                    alert(msg);
                },
                eventTextColor: '#fff',
                eventBackgroundColor: 'rgba(29, 161, 242, 0.6)',
                eventBorderColor: 'transparent',
                eventClick: function(info) {
                    editEvent(info.event);
                }
            });
            calendar.render();
        });

        function editEvent(event) {
            openEventModal();
            document.getElementById('modalTitle').innerText = 'Termin bearbeiten';
            document.getElementById('eventId').value = event.id;
            
            const form = document.getElementById('eventForm');
            form.title.value = event.title;
            
            const startStr = event.start.toISOString();
            form.start_date.value = startStr.substring(0, 10);
            
            if (event.allDay) {
                form.all_day.checked = true;
                document.getElementById('timeFields').style.display = 'none';
                form.start_time.value = '';
                form.end_time.value = '';
            } else {
                form.all_day.checked = false;
                document.getElementById('timeFields').style.display = 'grid';
                form.start_time.value = startStr.substring(11, 16);
                if (event.end) {
                    form.end_time.value = event.end.toISOString().substring(11, 16);
                }
            }
            
            form.location.value = event.extendedProps.location || '';
            form.description.value = event.extendedProps.description || '';
            
            document.getElementById('deleteBtn').style.display = 'inline-block';
        }

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
        // Event Modal Logic
        let eventModal, eventForm;

        document.addEventListener('DOMContentLoaded', function() {
            eventModal = document.getElementById('eventModal');
            eventForm = document.getElementById('eventForm');

            if (eventForm) {
                document.getElementById('all_day').addEventListener('change', function(e) {
                    const timeFields = document.getElementById('timeFields');
                    if (timeFields) timeFields.style.display = e.target.checked ? 'none' : 'grid';
                });

                eventForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const submitBtn = eventForm.querySelector('button[type="submit"]');
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Speichere...';

                    const eventId = document.getElementById('eventId').value;
                    const url = eventId ? `{{ url('/calendar/event') }}/${eventId}` : "{{ route('calendar.store') }}";
                    const method = eventId ? 'PUT' : 'POST';

                    const formData = new FormData(eventForm);
                    
                    // Workaround for PUT with FormData if browser/server constraints exist, 
                    // but Laravel handles _method or JSON better for PUT.
                    // We'll use JSON if it's a PUT.
                    
                    let fetchOptions;
                    if (method === 'PUT') {
                        const data = {};
                        formData.forEach((value, key) => { data[key] = value; });
                        fetchOptions = {
                            method: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(data)
                        };
                    } else {
                        fetchOptions = {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        };
                    }
                    
                    try {
                        const response = await fetch(url, fetchOptions);
                        const result = await response.json();

                        if (result.success) {
                            alert('Erfolg: ' + result.message);
                            location.reload();
                        } else {
                            alert('Fehler: ' + result.message);
                        }
                    } catch (error) {
                        alert('Ein Fehler ist aufgetreten: ' + error.message);
                    } finally {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Speichern';
                    }
                });
            }
        });

        async function deleteEvent() {
            const eventId = document.getElementById('eventId').value;
            if (!eventId) return;

            if (!confirm('Möchten Sie diesen Termin wirklich löschen?')) return;

            const deleteBtn = document.getElementById('deleteBtn');
            deleteBtn.disabled = true;
            deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            try {
                const response = await fetch(`{{ url('/calendar/event') }}/${eventId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();
                if (result.success) {
                    alert('Termin gelöscht!');
                    location.reload();
                } else {
                    alert('Fehler: ' + result.message);
                }
            } catch (error) {
                alert('Ein Fehler ist aufgetreten: ' + error.message);
            } finally {
                deleteBtn.disabled = false;
                deleteBtn.innerHTML = 'Löschen';
            }
        }
            }
        });

        function openEventModal() {
            if (eventModal) {
                eventModal.style.display = 'flex';
                document.getElementById('modalTitle').innerText = 'Neuer Termin';
                document.getElementById('eventId').value = '';
                document.getElementById('deleteBtn').style.display = 'none';
            }
        }

        function closeEventModal() {
            if (eventModal) eventModal.style.display = 'none';
            if (eventForm) {
                eventForm.reset();
                document.getElementById('timeFields').style.display = 'grid';
            }
        }

        window.addEventListener('resize', resize);
        resize();
        animate();
    </script>

    <!-- Event Modal -->
    <div id="eventModal" class="modal-overlay" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
        <div class="card" style="width: 100%; max-width: 500px; padding: 30px;">
            <div class="card-header" style="margin-bottom: 25px;">
                <h2 id="modalTitle"><i class="fas fa-calendar-plus"></i> Neuer Termin</h2>
                <button onclick="closeEventModal()" style="background:none; border:none; color: var(--text-muted); cursor: pointer; font-size: 1.2rem;">&times;</button>
            </div>
            <form id="eventForm">
                @csrf
                <input type="hidden" id="eventId" name="event_id">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 5px;">Titel</label>
                    <input type="text" name="title" required style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 8px; padding: 10px; color: #fff;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 5px;">Datum</label>
                    <input type="date" name="start_date" required style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 8px; padding: 10px; color: #fff;">
                </div>
                <div id="timeFields" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 5px;">Startzeit</label>
                        <input type="time" name="start_time" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 8px; padding: 10px; color: #fff;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 5px;">Endzeit</label>
                        <input type="time" name="end_time" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 8px; padding: 10px; color: #fff;">
                    </div>
                </div>
                <div style="margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                    <input type="checkbox" name="all_day" id="all_day" value="1">
                    <label for="all_day" style="font-size: 0.85rem;">Ganztägiger Termin</label>
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 5px;">Ort (optional)</label>
                    <input type="text" name="location" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 8px; padding: 10px; color: #fff;">
                </div>
                <div style="margin-bottom: 25px;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 5px;">Beschreibung (optional)</label>
                    <textarea name="description" rows="3" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 8px; padding: 10px; color: #fff; resize: vertical;"></textarea>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="button" id="deleteBtn" onclick="deleteEvent()" style="display:none; background: #ff4444; border: none; color: #fff; padding: 12px; border-radius: 8px; cursor: pointer; font-weight: 600;">Löschen</button>
                    <button type="button" onclick="closeEventModal()" style="flex: 1; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); color: #fff; padding: 12px; border-radius: 8px; cursor: pointer;">Abbrechen</button>
                    <button type="submit" style="flex: 2; background: var(--primary-accent); border: none; color: #fff; padding: 12px; border-radius: 8px; cursor: pointer; font-weight: 600;">Speichern</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
