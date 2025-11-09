<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

checkAdminAuth();

// Перенаправляем на dashboard по умолчанию
header("Location: dashboard.php");
exit;
?>