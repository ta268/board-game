<?php
require_once __DIR__ . '/auth_check.php';

if (!isset($_SESSION['is_admin']) || (int)$_SESSION['is_admin'] !== 1) {
    header('Location: index.php');
    exit;
}
