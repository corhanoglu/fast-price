<?php
/* =============================================================================
   DOSYA: logout.php
   AÇIKLAMA: Güvenli çıkış işlemi yapar.
============================================================================= */
include 'config.php';
$_SESSION = [];
session_destroy();
header('Location: login.php');
exit;
?>
