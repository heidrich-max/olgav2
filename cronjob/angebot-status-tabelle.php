<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
try {
	$local_db = new PDO('mysql:host=localhost;dbname=cms_frankgroup', 'cms_frankgroup', 'tpU~1t787');
} catch (PDOException $e) {
	echo 'Verbindung fehlgeschlagen: ' . $e->getMessage();
}


$status = $local_db->query("SELECT angebot_id, projekt_id, max(status) as maxstatus FROM angebot_status_a, angebot_status WHERE angebot_status_a.status = angebot_status.id GROUP BY angebot_id, projekt_id");

foreach($status as $stati)
{
	$angebot_id = $stati['angebot_id'];
	$projekt_id = $stati['projekt_id'];
	$maxstatus = $stati['maxstatus'];
	
	
	$letzter_status = $local_db->query("SELECT status_sh FROM angebot_status WHERE id = '$maxstatus'")->fetchColumn();
	$letzter_status_name = $local_db->query("SELECT status_lg FROM angebot_status WHERE id = '$maxstatus'")->fetchColumn();
	$letzter_status_bg_hex = $local_db->query("SELECT bg FROM angebot_status WHERE id = '$maxstatus'")->fetchColumn();
	$letzter_status_farbe_hex  = $local_db->query("SELECT color FROM angebot_status WHERE id = '$maxstatus'")->fetchColumn();
				
	if($letzter_status == "A" || $letzter_status == "ANG")
	{
		$abgeschlossen_status = 'Angebot abgeschlossen';
	}
	else
	{
		$abgeschlossen_status = 'Angebot nicht abgeschlossen';
	}
	
	$angebot_id_count = $local_db->query( "SELECT COUNT(id) as angebot_id_count FROM angebot_tabelle WHERE projekt_id = $projekt_id AND angebot_id = $angebot_id" )->fetchColumn();			
	
	if($angebot_id_count > 0)
	{
		$statement= $local_db->prepare("UPDATE angebot_tabelle SET letzter_status =:letzter_status, letzter_status_name =:letzter_status_name, letzter_status_bg_hex =:letzter_status_bg_hex, letzter_status_farbe_hex =:letzter_status_farbe_hex, abgeschlossen_status =:abgeschlossen_status WHERE angebot_id =:angebot_id AND projekt_id =:projekt_id");
		$statement->execute(array('letzter_status' => "$letzter_status", 'letzter_status_name' => "Status $letzter_status_name", 'letzter_status_bg_hex' => "$letzter_status_bg_hex", 'letzter_status_farbe_hex' => "$letzter_status_farbe_hex", 'abgeschlossen_status' => "$abgeschlossen_status", 'angebot_id' => $angebot_id, 'projekt_id' => $projekt_id));
	}
	
}
