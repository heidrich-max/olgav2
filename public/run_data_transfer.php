<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=cms_frankgroup', 'cms_frankgroup', 'tpU~1t787');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h1>Starting Data Transfer...</h1>";

    // 1. Get all base data from old auftrag_projekt
    $stmt = $pdo->query("SELECT * FROM auftrag_projekt");
    $oldEntries = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($oldEntries as $old) {
        $pId = $old['id'];
        $host = $old['host'];
        $port = $old['port'];
        $user = $old['email_pas'];
        $pass = $old['passwort'];
        $fromName = $old['firmenname'];
        $fromEmail = $old['email_pas'];

        echo "Processing Project ID $pId ({$old['projekt']})... ";

        // Update the new fields in auftrag_projekt_firma where projekt_id matches
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
            'host' => $host,
            'port' => $port,
            'user' => $user,
            'pass' => $pass,
            'fromEmail' => $fromEmail,
            'fromName' => $fromName,
            'pId' => $pId
        ]);

        echo "Updated " . $updateStmt->rowCount() . " rows.<br>";
    }

    echo "<h2>Transfer completed!</h2>";

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
