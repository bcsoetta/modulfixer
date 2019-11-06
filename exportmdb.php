<?php
include 'include.php';

$noData = false;
$noAlter = false;

// it has to be run with 2 arguments only
if ($argc < 2) {
    echo "Usage - php exportmdb.php [mdb-file] [-nd] [-na]\n";
    echo "-nd: no data, -na: no alter\n";
    echo "Output complete sql insert statements in timestamped\n";
    echo "SQL File\n";
    exit();
} 

try {
    // parse the remaining parameter as arguments
    echo "Parsing command...{$argc}\n";
    $p = 2;
    while ($p < $argc) {
        $arg = trim($argv[$p++]);
        echo "Found arguments: {$arg}\n";
        if ($arg == '-nd') {
            $noData = true;
        }
        if ($arg == '-na') {
            $noAlter = true;
        }
    }
    // try to grab exported filename
    // var_dump($tableNames);
    $fname = generateTimestampedFilename("sql");

    // ok, open db
    echo "Opening db : {$argv[1]}...\n";
    $db = openDB($argv[1]);

    // ok, now we generate each table exports, and append to file
    $sqlStatements = [];
    foreach ($tableNames as $tblName) {
        echo "====================================================\n";
        echo "table: {$tblName}\n";
        if (!$noAlter) {
            echo "Generating alter table for table: {$tblName}...\n";
            $sqlAlters = createAlter($tblName);
            if ($sqlAlters !== false) {
                $cnt = count($sqlAlters);
                echo "Table {$tblName} generated {$cnt} alters.\n";
                $sqlStatements = array_merge($sqlStatements, $sqlAlters);
            } else {
                echo "--No alters. Phew!\n";
            }
        }
        
        if (!$noData) {
            echo "Generating inserts for table: {$tblName}...\n";
            $sqlInserts = generateExportSQLString($db, $tblName);
            // $sqlInserts = [];
            // if generated, write it down? or just add it?
            if ($sqlInserts !== false) {
                $cnt = count($sqlInserts);
                echo "Table {$tblName} generated {$cnt} inserts.\n";
                $sqlStatements = array_merge($sqlStatements, $sqlInserts);
            } else {
                throw new \Exception("Error generating sql inserts for table: {$tblName}");
            }
            echo "\n";
        }
        
    }   
    // got it!
    $count = count($sqlStatements);
    echo "+++++++++++++++++++++++++++++++++++++++++++++++++++\n";
    echo "Generated total {$count} inserts!\n";
    echo "Writing to result sql file...\n";
    // $concat = implode("",$sqlStatements);
    if (file_put_contents($fname, $sqlStatements) === false) {
        throw new \Exception("Error writing sql result");        
    }
    // good we succeeds 
    echo "Success exporting entire database to {$fname}";
} catch (\Exception $e) {
    die('Error: ' . $e->getMessage());
}