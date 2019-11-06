<?php
include 'include.php';

// copy data from db1 to db2
function copyTable($db1, $db2, $tableName, $deleteFirst=false) {
    echo "Copying table: {$tableName}...\n";
    if (!$db1) {
        throw new \Exception("Source database not opened!");
    }

    if (!$db2) {
        throw new \Exception("Destination database not opened!");
    }

    $res = $db1->query("SELECT * FROM {$tableName}");
    if ($res) {
        // if we got something?
        $rows = $res->fetchAll(PDO::FETCH_ASSOC);
        $cnt = count($rows);

        if (!count($rows)) {
            echo "Query returns zero rows!\n";
            return false;
        } else {
            echo "Got {$cnt} rows...\n";
            // ok, grab column names
            $colNames = array_keys($rows[0]);

            // build prepared statements
            
            $cols = implode(",", array_map(function ($e) {
                return "[$e]";
            }, $colNames));
            // $vals = implode(",", array_map(function ($e) {
            //     return ':' . $e;
            // }, $colNames));
            $vals = implode(",", array_fill(0, count($colNames), '?'));
            $qStmt = "INSERT INTO {$tableName}({$cols}) VALUES ({$vals})";
            
            // set db2 to throw exception
            $db2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // echo it
            echo "=PREPARED STATEMENT=\n";
            echo "{$qStmt}\n";
            $ex = 0;
            $fail = 0;
            // set exception to catch shit
            
            try {
                echo "Building prepared statements...\n";
                // build prepared stetments
                $stmt = $db2->prepare($qStmt);
                // execute imports
                $executed = 0;
                $duplicate = 0;

                // deleting target table
                if ($deleteFirst) {
                    echo "Deleting contents from dst[$tableName]...\n";
                    $db2->query("DELETE FROM [$tableName]");
                    echo "Contents deleted\n\n";
                }

                foreach ($rows as $row) {
                    // surround in try catch too?
                    try {
                        $stmt->execute(array_values($row));
                        
                        ++$executed;
                    } catch (PDOException $pe) {
                        // if it's just duplicate, handle it gracefully
                        if ($pe->getCode() == '23000') {
                            // normal, continue operation
                            // echo "Duplicate row:\n";
                            ++$duplicate;
                            continue;
                        } else {
                            throw new \Exception($pe->getMessage());  // let outer catch handle it
                        }
                    }
                    echo "\rEx: {$executed}, Dup: {$duplicate}";
                }
                echo "\n";
            } catch (\Exception $e) {
                //throw $th;
                echo "Last Error: [{$e->getCode()}] - {$e->getMessage()}\n";
            }
            
            echo "\n";
        }
    } else {
        throw new \Exception("error querying source @ table {$tableName}!");
    }
}

if ($argc != 3) {
    echo "php copydb.php [src_db] [dst_db]\n";
    echo "copy contents of [src_db] into [dst_db]\n";
} else {
    try {
        // read src_db
        echo "Opening source db...\n";
        $srcDb = openDB($argv[1]);

        echo "Opening destination db...\n";
        $dstDb = openDB($argv[2]);

        $tables = [
            /* 'tblData',
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
            'tblPibTrf',
            'tblPibNpt', 
            'tblPIBNTB', 
            'tblPibPgt', 
            'tblPibRes', 
            'TblPIBResBill', 
            'TblPIBResNPBL', 
            'TblPIBResNPD' */
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

        // run copy table
        foreach ($tables as $tableName) {
            copyTable($srcDb, $dstDb, $tableName, true);
        }
        // copyTable($srcDb, $dstDb, 'tblPibDtl');

        // for now, that's it...
        echo "Script done\n";
    } catch (\Exception $e) {
        error_log($e->getMessage());
    }
}