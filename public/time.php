<?php
echo "Server Time: " . date("Y-m-d H:i:s") . "<br>";
echo "ls.php local file time: " . date("Y-m-d H:i:s", filemtime(__DIR__ . '/ls.php')) . "<br>";
echo "ls.php root file time: " . date("Y-m-d H:i:s", filemtime(__DIR__ . '/../ls.php')) . "<br>";
