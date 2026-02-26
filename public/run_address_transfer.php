<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=cms_frankgroup', 'cms_frankgroup', 'tpU~1t787');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h1>Starting Address Data Transfer...</h1>";

    $stmt = $pdo->query("SELECT * FROM auftrag_projekt");
    $oldEntries = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($oldEntries as $old) {
        $pId = $old['id'];
        echo "Processing Project ID $pId ({$old['projekt']})... ";

        $updateStmt = $pdo->prepare("
            UPDATE auftrag_projekt_firma 
            SET strasse = :strasse,
                plz = :plz,
                ort = :ort,
                telefon = :telefon,
                inhaber = :inhaber,
                ust_id = :ust_id,
                handelsregister = :handelsregister
            WHERE projekt_id = :pId
        ");

        $updateStmt->execute([
            'strasse' => $old['strasse'] ?? '',
            'plz' => $old['plz'] ?? '',
            'ort' => $old['ort'] ?? '',
            'telefon' => $old['telefon'] ?? '',
            'inhaber' => $old['inhaber'] ?? '',
            'ust_id' => $old['ust_id'] ?? '',
            'handelsregister' => $old['handelsregister'] ?? '',
            'pId' => $pId
        ]);

        echo "Updated " . $updateStmt->rowCount() . " rows.<br>";
    }

    echo "<h2>Address Transfer completed!</h2>";

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
