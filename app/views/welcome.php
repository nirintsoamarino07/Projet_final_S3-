<?php

$title = 'Dashboard - S3Final';
$headerTitle = 'Dashboard';
$pageTitle = 'Dashboard';

ob_start();
include __DIR__ . '/pages/dashboard.php';
$content = ob_get_clean();

include __DIR__ . '/layouts/base.php';