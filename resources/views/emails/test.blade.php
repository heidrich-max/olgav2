<!DOCTYPE html>
<html>
<head>
    <title>Olga Test Mail</title>
</head>
<body>
    <h1>Dies ist eine Test-E-Mail vom Projekt: {{ $projectName }}</h1>
    <p>Wenn du diese Mail erhältst, funktioniert die SMTP-Konfiguration für dieses Projekt in Olga v2 einwandfrei!</p>
    <hr>
    <p>Gesendet am: {{ date('d.m.Y H:i:s') }}</p>
</body>
</html>
