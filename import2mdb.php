<?php
include 'include.php';

/**
 * import2mdb.php
 * Imports sql file and execute it into mdb file
 */

 if ($argc !== 3) {
     echo "Usage: php import2mdb.php [sql_file] [mdb_file]\n";
     echo "Imports [sql_file] into [mdb_file], an access database (.mdb)\n";

    //  print_r(createAlter('tblPibDtl'));
 } else {
     try {
         // set error log
         ini_set('log_errors', 1);
         ini_set('error_log', './error.log');

         // load database
         echo "Opening database...\n";
         $db = openDB($argv[2]);

         // read sql statements
         echo "Reading sql statements...\n";
         $sqlStatements = file_get_contents($argv[1]);

         if (!$sqlStatements) {
             throw new \Exception("Error parsing sql statements!\n");
         }

        //  $db->exec($sqlStatements);
        $res = $db->query($sqlStatements);
        
        if (!$res) {
            $err = $db->errorInfo();
            $errMsg = "Error: SQLSTATE[{$err[0]}] -> {$err[2]}";
            throw new \Exception($errMsg);
        }

         exit();

         // break per line
         $stmts = explode("\n", $sqlStatements);
         $cnt = count($stmts);

         echo "Executing {$cnt} sql inserts...\n";

        $i = 1;
        $executed = 0;
        $skipped = 0;
        $errorCount = 0;
         foreach ($stmts as $stmt) {
             // skip empty
             if (strlen(trim($stmt)) < 2) {
                 continue;
             }

             echo "\rStatement {$i} of {$cnt} : executed({$executed}), skipped({$skipped}), error({$errorCount})...";
             $res = $db->query($stmt);

             if ($res) {
                 $executed++;
             } else {
                 $err = $db->errorInfo();
                 if ($err[0] == '23000') {
                     $skipped++;
                 } else {
                    // echo "Failed :( \n\n";
                    // echo "Error @ '{$stmt}'\n";
                    // echo "{$err[0]} : {$err[2]}\n";
                    error_log("Failed @ stmt '{$stmt}'\nSQLSTATE[{$err[0]}] : {$err[2]}\n" );
                    $errorCount++;
                 }
                 // throw error
             }

             $i++;
         }
         echo "\n";

         // try to execute it
        //  echo "Executing sql statements...\n";
        //  $db->exec($sqlStatements);

         echo "Successfully execute sql statements!";
     } catch (\Exception $e) {
         die("Error: " . $e->getMessage());
     }
 }