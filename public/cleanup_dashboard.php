<?php
$public = __DIR__;
$filesToRemove = [
    'create_dashboard_models.php',
    'setup_dashboard_logic.php',
    'create_dashboard_view.php',
    'db_inspect.php',
    'copy_assets.php',
    'update_login_design.php'
];

foreach ($filesToRemove as $file) {
    if (file_exists($public . '/' . $file)) {
        unlink($public . '/' . $file);
        echo "Removed: $file\n";
    }
}
?>
