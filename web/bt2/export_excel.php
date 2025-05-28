<?php
require_once 'functions.php';
session_start();

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

global $mysqli;
$tables = [];
$result = $mysqli->query("SHOW TABLES");
while ($row = $result->fetch_array()) {
    $tables[] = $row[0];
}

$zip = new ZipArchive();
$tmpZip = tempnam(sys_get_temp_dir(), 'zip');
$zip->open($tmpZip, ZipArchive::OVERWRITE);

foreach ($tables as $table) {
    $csvHandle = fopen('php://temp', 'w+');
    $res = $mysqli->query("SELECT * FROM `$table`");
    if ($res) {
        // Header
        $fields = $res->fetch_fields();
        $header = [];
        foreach ($fields as $field) {
            $header[] = $field->name;
        }
        fputcsv($csvHandle, $header);

        // Rows
        while ($row = $res->fetch_assoc()) {
            fputcsv($csvHandle, $row);
        }
        rewind($csvHandle);
        $csvContent = stream_get_contents($csvHandle);
        fclose($csvHandle);

        $zip->addFromString($table . '.csv', $csvContent);
    }
}
$zip->close();

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="database_export.zip"');
header('Content-Length: ' . filesize($tmpZip));
readfile($tmpZip);
unlink($tmpZip);
exit;