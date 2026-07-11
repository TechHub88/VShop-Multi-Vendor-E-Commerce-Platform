<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = DB::select('SHOW TABLES');
$dbName = DB::connection()->getDatabaseName();
$key = 'Tables_in_' . $dbName;

$sql = "-- vshop Database Export\n";
$sql .= "-- Generated: " . now() . "\n";
$sql .= "-- Database: {$dbName}\n\n";
$sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

$count = 0;
foreach ($tables as $table) {
    $tableName = $table->$key;
    $count++;
    echo "Exporting table {$count}: {$tableName}\n";
    
    $create = DB::select("SHOW CREATE TABLE `{$tableName}`");
    $createSql = $create[0]->{'Create Table'} ?? '';
    
    $sql .= "-- Table: {$tableName}\n";
    $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
    $sql .= $createSql . ";\n\n";
    
    $rows = DB::select("SELECT * FROM `{$tableName}`");
    
    if (count($rows) > 0) {
        $columns = array_keys((array) $rows[0]);
        $colList = '`' . implode('`, `', $columns) . '`';
        
        foreach (array_chunk($rows, 100) as $chunk) {
            $sql .= "INSERT INTO `{$tableName}` ({$colList}) VALUES\n";
            $values = [];
            foreach ($chunk as $row) {
                $rowVals = [];
                foreach ((array) $row as $val) {
                    if (is_null($val)) {
                        $rowVals[] = 'NULL';
                    } else {
                        $rowVals[] = "'" . addslashes((string)$val) . "'";
                    }
                }
                $values[] = '(' . implode(', ', $rowVals) . ')';
            }
            $sql .= implode(",\n", $values) . ";\n\n";
        }
    }
}

$sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

$path = 'C:\\Users\\Sayani Roy\\Downloads\\vshop_database.sql';
file_put_contents($path, $sql);

echo "\n=== DONE ===\n";
echo "File: {$path}\n";
echo "Size: " . round(filesize($path) / 1024 / 1024, 2) . " MB\n";
echo "Tables: {$count}\n";
