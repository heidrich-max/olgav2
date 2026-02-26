<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>OLGA - Projekt bearbeiten</title>
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
            background: radial-gradient(circle at top left, #1a2a44, #0f172a, #070b14);
            color: var(--text-main);
            min-height: 100vh;
        }

        .navbar {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(15px);
            padding: 12px 40px;
            display: flex; justify-content: space-between; align-items: center;
            border-bottom: 1px solid var(--glass-border);
        }

        .navbar img { height: 38px; }

        .container { padding: 40px; max-width: 800px; margin: 0 auto; }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        h1 { font-size: 2rem; font-weight: 700; background: linear-gradient(90deg, #fff, var(--primary-accent)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        
        .btn-back {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: white;
            padding: 10px 20px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            display: flex; align-items: center; gap: 8px;
            transition: all 0.3s ease;
        }
        .btn-back:hover { background: rgba(255,255,255,0.2); }

        .card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5);
        }

        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-size: 0.9rem; color: var(--text-muted); font-weight: 600; }
        
        .form-control {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 12px 15px;
            color: white;
            font-family: inherit;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .form-control:focus { outline: none; border-color: var(--primary-accent); background: rgba(255, 255, 255, 0.1); }

        select.form-control option { background: #0f172a; color: white; }

        .color-input-wrapper {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .color-picker {
            width: 50px;
            height: 50px;
            padding: 0;
            border: none;
            background: none;
            cursor: pointer;
        }

        .btn-save {
            background: var(--primary-accent);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 10px;
        }
        .btn-save:hover { opacity: 0.9; transform: translateY(-2px); }

        .error-message {
            color: #fca5a5;
            font-size: 0.8rem;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <img src="/logo/olga_neu.svg" alt="Frank Group">
    </nav>

    <div class="container">
        <div class="header-section">
            <h1>Projekt bearbeiten</h1>
            <a href="{{ route('companies.index') }}" class="btn-back"><i class="fas fa-arrow-left"></i> Abbrechen</a>
        </div>

        <div class="card">
            <form action="{{ route('companies.update', $project->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">Projektname / Firmenname</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $project->name) }}" required>
                    @error('name') <div class="error-message">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="name_kuerzel">Projekt-Kürzel</label>
                    <input type="text" name="name_kuerzel" id="name_kuerzel" class="form-control" value="{{ old('name_kuerzel', $project->name_kuerzel) }}" required>
                    @error('name_kuerzel') <div class="error-message">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="firma_id">Zugehörige Hauptfirma</label>
                    <select name="firma_id" id="firma_id" class="form-control" required>
                        @foreach($companyNames as $id => $name)
                            <option value="{{ $id }}" {{ old('firma_id', $project->firma_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('firma_id') <div class="error-message">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="bg">Projekt-Farbe (Hex)</label>
                    <div class="color-input-wrapper">
                        <input type="color" id="color_picker" class="color-picker" value="{{ $project->bg }}">
                        <input type="text" name="bg" id="bg" class="form-control" value="{{ old('bg', $project->bg) }}" required placeholder="#000000">
                    </div>
                    @error('bg') <div class="error-message">{{ $message }}</div> @enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label for="strasse">Straße</label>
                        <input type="text" name="strasse" id="strasse" class="form-control" value="{{ old('strasse', $project->strasse) }}">
                        @error('strasse') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="ort">Ort</label>
                        <input type="text" name="ort" id="ort" class="form-control" value="{{ old('ort', $project->ort) }}">
                        @error('ort') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 120px 1fr; gap: 20px;">
                    <div class="form-group">
                        <label for="plz">PLZ</label>
                        <input type="text" name="plz" id="plz" class="form-control" value="{{ old('plz', $project->plz) }}">
                        @error('plz') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="telefon">Telefon</label>
                        <input type="text" name="telefon" id="telefon" class="form-control" value="{{ old('telefon', $project->telefon) }}">
                        @error('telefon') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">E-Mail</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $project->email) }}">
                    @error('email') <div class="error-message">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="inhaber">Inhaber</label>
                    <input type="text" name="inhaber" id="inhaber" class="form-control" value="{{ old('inhaber', $project->inhaber) }}">
                    @error('inhaber') <div class="error-message">{{ $message }}</div> @enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label for="ust_id">USt-IdNr.</label>
                        <input type="text" name="ust_id" id="ust_id" class="form-control" value="{{ old('ust_id', $project->ust_id) }}">
                        @error('ust_id') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="handelsregister">Handelsregister</label>
                        <input type="text" name="handelsregister" id="handelsregister" class="form-control" value="{{ old('handelsregister', $project->handelsregister) }}">
                        @error('handelsregister') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                </div>

                <button type="submit" class="btn-save">Änderungen speichern</button>
            </form>
        </div>
    </div>

    <script>
        const colorPicker = document.getElementById('color_picker');
        const colorInput = document.getElementById('bg');

        colorPicker.addEventListener('input', (e) => {
            colorInput.value = e.target.value.toUpperCase();
        });

        colorInput.addEventListener('input', (e) => {
            let val = e.target.value;
            if (val.length === 7 && val.startsWith('#')) {
                colorPicker.value = val;
            }
        });
    </script>
</body>
</html>
