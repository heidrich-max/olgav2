<?php
$db = new PDO('mysql:host=127.0.0.1;dbname=cms_frankgroup;charset=utf8', 'cms_frankgroup', 'tpU~1t787');
$res = $db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
print_r($res);
