<?php
try {
    $db = new PDO('mysql:host=127.0.0.1;dbname=cms_frankgroup;charset=utf8', 'cms_frankgroup', 'tpU~1t787');
    $res = $db->query('DESCRIBE auftrag_projekt_wawi')->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($res, JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
