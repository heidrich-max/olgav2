# dev.frankgroup.net — CLAUDE.md

## Projektübersicht

Laravel 10 CMS-Anwendung (PHP 8.1). Neue Version von **cms.frankgroup.net** — beide Systeme teilen sich dieselbe MySQL-Datenbank und dieselbe JTL Wawi-Instanz (SQL Server). Deployment via FTP auf einen Plesk-Server.

---

## KRITISCHE REGELN — immer beachten

### 1. Geteilte Datenbank (MySQL)
- Diese App und **cms.frankgroup.net greifen auf DIESELBE MySQL-Datenbank zu**.
- Jede Schemaänderung kann das Live-System cms.frankgroup.net sofort kaputt machen.
- **Niemals `php artisan migrate` ausführen ohne explizite Bestätigung des Nutzers.**
- Vor jeder neuen Migration: prüfen, ob die betroffene Tabelle von cms.frankgroup.net genutzt wird.
- Keine `DROP COLUMN`, `RENAME COLUMN`, `DROP TABLE` Operationen ohne gründliche Analyse und Rücksprache.
- Neue Spalten nur als nullable oder mit Default-Wert hinzufügen.

### 2. JTL Wawi (SQL Server) — NUR LESEN
- Die App verbindet sich via PDO-SQLServer zu JTL Wawi-Mandanten.
- Verbindungsdaten stehen in der Tabelle `auftrag_projekt_wawi`.
- **Die Wawi-Datenbank ist absolut read-only** — niemals INSERT/UPDATE/DELETE auf SQL Server schreiben.
- Die Import-Commands (`app:import-jtl-orders`, `app:import-jtl-offers`, etc.) lesen aus Wawi und schreiben nur in die lokale MySQL-DB.

### 3. Cronjobs laufen kontinuierlich
Folgende Scheduled Commands laufen im Hintergrund auf dem Server:
- `orders:sync-statuses` — **jede Minute**
- `app:import-jtl-offers`, `app:import-jtl-order-articles`, `app:import-jtl-orders` — **alle 5 Minuten**
- `app:generate-offer-todos`, `wiedervorlage:process`, `app:process-overdue-deliveries`, `app:process-bo-status-orders` — täglich 06:00

Schemaänderungen an den betroffenen Tabellen können laufende Jobs zum Absturz bringen.

---

## Deployment — SFTP direkt auf dem Server

> **ACHTUNG: Das Arbeitsverzeichnis `o:\dev.frankgroup.net` ist per SFTP direkt mit dem Produktionsserver verbunden.**
> **Jede Dateiänderung ist sofort live. Es gibt kein Staging, kein Review vor dem Deploy.**

- Änderungen an PHP-Dateien (Controller, Models, Routes, Views) wirken sofort.
- Vor jeder Änderung überlegen: Kann das den laufenden Betrieb unterbrechen?
- Keine Experimente oder unfertige Zwischenstände speichern.
- Die `.env`-Datei auf dem Server niemals überschreiben.
- `vendor/` und `node_modules/` nicht anfassen — bereits auf dem Server vorhanden.
- Nach Composer-Änderungen: `composer install --no-dev` auf dem Server per SSH/Terminal ausführen.
- Frontend-Assets (`public/build/`): `npm run build` lokal ausführen, dann nur `public/build/` hochladen.

### Empfehlung: Git einrichten
- Lokal ist Git bereits initialisiert (`.gitignore` vorhanden).
- Ein Remote-Repo (GitHub/GitLab privat) einrichten, um Änderungen zu versionieren.
- Plesk unterstützt Git-Deployment nativ — wäre die sauberste Lösung.

---

## Sicherheitshinweise

### Debug-Routen in routes/web.php
Folgende Routen existieren und sind ein Sicherheitsrisiko in Production:
- `/inspect-wawi` — gibt Wawi-Datenbankstruktur und Sample-Daten aus (**öffentlich zugänglich**)
- `/debug-db` — listet alle MySQL-Tabellen und -Spalten auf (**öffentlich zugänglich**)
- `/run-migrations` — führt Migrationen per HTTP-Request aus (nur auth, aber trotzdem gefährlich)
- `/debug-smtp/{id}` — gibt SMTP-Konfiguration aus (nur auth)

Diese Routen sollten in Production entfernt oder mit einer Admin-only Middleware geschützt werden.

---

## Wichtige Tabellen (MySQL, geteilt mit cms.frankgroup.net)

| Tabelle | Beschreibung |
|---|---|
| `auftrag_tabelle` | Aufträge (wird von Import-Command beschrieben) |
| `angebot_tabelle` | Angebote |
| `auftrag_projekt` | Projekte/Mandanten |
| `auftrag_projekt_firma` | Firmen zu Projekten |
| `auftrag_projekt_wawi` | Wawi-Verbindungsdaten (enthält Passwörter!) |
| `auftrag_status_a` | Auftragsstatus-Historie |
| `angebot_status_*` | Angebotsstatus-Tabellen |
| `hersteller*` | Hersteller-Verwaltung |
| `todos` | Todo-Einträge |
| `user` | Benutzer |
| `produkte`, `produkt_varianten`, `produkt_druck_positionen`, `produkt_preise` | Produktdaten |

---

## Lokale Entwicklung

```bash
# Dependencies installieren
composer install
npm install

# Frontend-Assets bauen
npm run dev       # Entwicklung mit Hot Reload
npm run build     # Production Build

# Migrationen (NUR lokal, niemals ohne Rücksprache auf Production)
php artisan migrate

# Scheduled Commands manuell testen
php artisan app:import-jtl-orders
```

---

## Dateistruktur — Wichtiges

- [app/Console/Commands/](app/Console/Commands/) — alle Import- und Verarbeitungs-Cronjobs
- [app/Http/Controllers/DashboardController.php](app/Http/Controllers/DashboardController.php) — Hauptcontroller
- [app/Models/](app/Models/) — Eloquent-Models (Tabellennamen beachten, keine Standard-Laravel-Namen)
- [routes/web.php](routes/web.php) — alle Routen inkl. der zu entfernenden Debug-Routen
- [database/migrations/](database/migrations/) — Migrationshistorie
