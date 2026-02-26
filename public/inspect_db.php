<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=cms_frankgroup', 'cms_frankgroup', 'tpU~1t787');
    
    echo "<h1>Data Transfer Check</h1>";
    $stmt = $pdo->query("SELECT * FROM auftrag_projekt");
    $oldEntries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($oldEntries as $old) {
        $updateStmt = $pdo->prepare("
            UPDATE auftrag_projekt_firma 
            SET smtp_host = :host,
                smtp_port = :port,
                smtp_user = :user,
                smtp_password = :pass,
                smtp_encryption = 'tls',
                mail_from_address = :fromEmail,
                mail_from_name = :fromName
            WHERE projekt_id = :pId
        ");
        $updateStmt->execute([
            'host' => $old['host'],
            'port' => $old['port'],
            'user' => $old['email_pas'],
            'pass' => $old['passwort'],
            'fromEmail' => $old['email_pas'],
            'fromName' => $old['firmenname'],
            'pId' => $old['id']
        ]);
        echo "Projekt {$old['projekt']}: Updated " . $updateStmt->rowCount() . " rows.<br>";
    }
    echo "<hr>";

    $interesting = ['angebot_tabelle', 'angebot_artikel', 'angebot_details', 'angebot_status', 'angebot_status_a', 'rechnung', 'liefer', 'adresse', 'projekt'];
    
    foreach ($tables as $table) {
        $isInteresting = false;
        foreach ($interesting as $keyword) {
            if (strpos($table, $keyword) !== false) {
                $isInteresting = true;
                break;
            }
        }
        
        if ($isInteresting) {
            echo "<h2>Columns in $table</h2>";
            $stmt = $pdo->query("DESCRIBE $table");
            $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "<table border='1'><tr><th>Field</th><th>Type</th></tr>";
            foreach ($cols as $col) {
                echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td></tr>";
            }
            echo "</table>";
            
            // Show sample data for offer tables
            if (strpos($table, 'angebot') !== false) {
                echo "<h3>Sample data (1 row) from $table</h3>";
                $stmt = $pdo->query("SELECT * FROM $table LIMIT 1");
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($data) {
                    echo "<pre>" . print_r($data, true) . "</pre>";
                }
            }
        }
    }

} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
