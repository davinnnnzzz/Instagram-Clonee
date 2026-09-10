<?php
// Simple DB connection test
include __DIR__ . '/config/koneksi.php';
if (isset($koneksi) && mysqli_ping($koneksi)) {
    echo "DB_OK\n";
} else {
    echo "DB_ERR: " . mysqli_connect_error() . "\n";
}
