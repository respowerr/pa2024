<?php
session_start();

if (isset($_POST['maintenance_mode_toggle']) && $_POST['maintenance_mode_toggle'] == 'on') {
    $_SESSION['maintenance_mode'] = 1;
} else {
    $_SESSION['maintenance_mode'] = 0;
}

if (isset($_SESSION['maintenance_mode']) && $_SESSION['maintenance_mode'] == 1) {
    header("Location: maintenance.php");
    exit;
}

?>
