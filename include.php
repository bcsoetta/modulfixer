<?php

// table names
$tableNames = [
    'tblBarang', 
    'tblData', 
    'tblData1', 
    'tblGudang', 
    'tblHistory', 
    'tblImportir', 
    'tblIzin', 
    'tblKapal', 
    'tblKemasan', 
    'tblKomLog', 
    'tblKpbc', 
    'tblKurs', 
    'tblNegara', 
    'tblNomor', 
    'tblPartner', 
    'tblPelDN', 
    'tblPelLN', 
    'tblPibCon', 
    'tblPibConR', 
    'tblPibDok', 
    'tblPibDtl', 
    'tblPibDtlDok', 
    'tblPibDtlFas', 
    'tblPibDtlLartas', 
    'tblPibDtlSpekKhusus', 
    'tblPibDtlVD', 
    'tblPibFas', 
    'tblPibHdr', 
    'tblPibKendaraan', 
    'tblPibKms', 
    'tblPibNpt', 
    'tblPIBNTB', 
    'tblPibPgt', 
    'tblPibRes', 
    'TblPIBResBill', 
    'TblPIBResNPBL', 
    'TblPIBResNPD', 
    'tblPibTrf', 
    'tblPpjk', 
    'tblRefCukai', 
    'tblSatuan', 
    'tblSetting', 
    'tblSpekKhusus', 
    'tblSupplier', 
    'tblTabel', 
    'tblTarif', 
    'tblValidasi', 
    'tblValuta', 
    'tmpcpib', 
    'tmpDPib'
];

// alterTableScripts!!! to match olden days data
// start with table with too much errors
function parseAlter($tableName) {
    // gotta check if it's available?
    $fname = 'alter/' . $tableName;
    if (!file_exists($fname)) {
        return false;
    }

    // it exists! read contents
    $content = file_get_contents($fname);
    $ret = array_map(function ($e) {
        return trim($e);
    }, explode(',', $content));

    return $ret;
}

// make the sql statements
function createAlter($tableName) {
    $cols = parseAlter($tableName);

    if (!$cols) {
        return false;
    }

    // return parsed
    return array_map(function ($e) use ($tableName) {
        return "ALTER TABLE {$tableName} ALTER COLUMN {$e}\n";
    }, $cols);
}

// fail!!! do not use
function copyTableContents($tblName, $db1, $db2) {
    // only proceeds if both succeeds
    if (!$db1) die ("source database error!\n");
    if (!$db2) die ("destination database error!\n");

    // say something
    echo "Copying table: {$tblName}...\n";
    echo "Generating sql inserts from source...\n";
    // read contents
    $sqlInserts = generateExportSQLString($db1, $tblName);
    if ($sqlInserts) {
        $count = count($sqlInserts);
        echo "Generated insert counts: {$count}\n";
        // go on, now execute em one by one
        echo "Executing each insert...\n";

        $i = 1;
        foreach ($sqlInserts as $sql) {
            // execute it
            if ($db2->query($sql)) {
                // succcess.. update counter
                echo "Inserted {$i} out of {$count}\r";
            } else {
                echo "Failed @ {$i} : '{$sql}'\n";
                return false;
            }
            $i++;
        }
        echo "\n";
        echo "Table copied successfully!\n";
        return true;
    }
    return false;
}

// open access database
function openDB($fullpath) {
    $fullpath = realpath($fullpath);
    $password = "MumtazFarisHana";
    // $conn = new COM("ADODB.Connection") or die("Cannot start ADODB");
    // // com_print_typeinfo($conn);
    // $db = $conn->Open(
    //     "Provider=Microsoft.ACE.OLEDB.12.0;" .
    //     "Data Source={$fullpath};" .
    //     "Password={$password};"
    // );
    $db = new PDO("odbc:DRIVER={Microsoft Access Driver (*.mdb, *.accdb)}; DBQ=$fullpath; Uid=Admin; Pwd=$password; ExtendedAnsiSQL=1;");
    // $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
    return $db;
}

// generate timestamped filename
function generateTimestampedFilename($ext) {
    return 'result-' . date('Y-m-d_H_i_s') . ".{$ext}";
}

function quote($txt) {
    return "'".preg_replace('/\'/', "''", $txt)."'";
}

// generate sql inserts
function generateExportSQLString($db, $tablename) {
    // first, query the database
    $result = $db->query("SELECT * FROM {$tablename};");
    if (!$result) {
        echo "Can't query table {$tablename}! probably doesn't exist.\n";
        return false;
    } else {
        // fetch em all
        $rows = $result->fetchAll(PDO::FETCH_ASSOC);

        // check if empty
        if (!count($rows)) {
            echo "Empty table! bailing...\n";
            return [];
        } 
        
        // there are data. strip column names
        $cols = array_keys($rows[0]);
        $colNames = implode(",", $cols);
        // ok, now we we write it
        $dumpSql = '';
        $sqlStatements = [];
        foreach ($rows as $row) {
            // strip values, quote it too
            $vals = array_map(function ($e) {
                // $e = trim($e);
                // return $db->quote($e);
                return quote($e);
            }, array_values($row)) ;
            $vals = implode(',', $vals);

            // build query
            $queryString = "INSERT INTO {$tablename}({$colNames}) VALUES ({$vals});\n";
            $dumpSql .= $queryString;
            $sqlStatements[] = $queryString;
        }
        return $sqlStatements;
    }
}
