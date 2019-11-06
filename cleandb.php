<?php
include 'include.php';
/**
 * clean all tables
 */

 function clearTable($db, $tblName) {
    echo "Clearing table: {$tblName}...\n";
    $db->query("DELETE FROM {$tblName}");
 }

if ($argc != 2) {
    echo "php cleandb.php [db_file]\n";
    echo "delete all rows from specified tables...\n";
} else {
    // execute all delete query
    try {
        $db = openDB($argv[1]);

        foreach ($tableNames as $tbl) {
            clearTable($db, $tbl);
        }
        
        echo "Script done\n";
    } catch (\Exception $e) {
        echo "Error: {$e->getMessage()}\n";
    }
}