<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Dieses Script fügt die neuen Adressfelder zur Tabelle 'auftrag_projekt_firma' hinzu.
 * Es kann direkt im Browser aufgerufen werden, sobald es auf den Server hochgeladen wurde.
 */

try {
    // Verbindung zur Datenbank (Daten aus inspect_db.php übernommen)
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=cms_frankgroup', 'cms_frankgroup', 'tpU~1t787');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h1>Datenbank-Update für Firmenverwaltung</h1>";

    $tableName = 'auftrag_projekt_firma';
    
    // Neue Spalten definieren
    $columns = [
        'strasse' => "VARCHAR(255) NULL",
        'plz' => "VARCHAR(10) NULL",
        'ort' => "VARCHAR(255) NULL",
        'telefon' => "VARCHAR(255) NULL",
        'email' => "VARCHAR(255) NULL",
        'inhaber' => "VARCHAR(255) NULL",
        'ust_id' => "VARCHAR(255) NULL",
        'handelsregister' => "VARCHAR(255) NULL"
    ];

    // Aktuelle Spalten abrufen
    $stmt = $pdo->query("DESCRIBE $tableName");
    $existingColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($columns as $column => $definition) {
        if (!in_array($column, $existingColumns)) {
            echo "Füge Spalte '$column' hinzu... ";
            $pdo->exec("ALTER TABLE $tableName ADD COLUMN $column $definition");
            echo "<span style='color:green;'>Erfolgreich</span><br>";
        } else {
            echo "Spalte '$column' existiert bereits. <br>";
        }
    }

    echo "<h2>Update abgeschlossen!</h2>";
    echo "<p>Du kannst dieses Script nun wieder vom Server löschen oder die Datei umbenennen.</p>";

} catch (PDOException $e) {
    echo '<h2 style="color:red;">Fehler beim Datenbank-Update:</h2> ' . $e->getMessage();
}
