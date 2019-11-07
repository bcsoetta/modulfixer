<?php
include 'include.php';

if ($argc != 2) {
    echo "php fixemptysppb.php [dpPib.mdb_file]\n";
    echo "fixes empty sppb response, manually though\n";
} else {
    try {
        // open the db
        echo "Opening db...\n";
        $db = openDB($argv[1]);

        // set mode
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // find how much empty sppb we found
        $res = $db->query("SELECT COUNT(*) AS empty FROM tblPibRes WHERE RESKD='300' AND DOKRESTG IS NULL");

        // check return
        if ($res) {
            $rows = $res->fetchAll(PDO::FETCH_ASSOC);
            $total = $rows[0]['empty'];

            echo "Found {$total} empty SPPB\n";

            // now, we iterate all over that
            $res = $db->query("SELECT * FROM tblPibRes WHERE RESKD='300' AND DOKRESTG IS NULL");

            if ($res) {
                $rows = $res->fetchAll(PDO::FETCH_ASSOC);
                $updated = [];
                // var_dump($rows);
                // $fp = fopen('php://input', 'r');
                foreach ($rows as $row) {
                    $newData = [
                        'CAR'       => $row['CAR'],
                        'DOKRESNO'  => '',
                        'DOKRESTG'  => ''
                    ];

                    echo "========================================================\n";
                    echo "input data utk CAR[{$row['CAR']}]:\n";
                    echo "nomor SPPB (xxxxxx/KPU.03/20xx): ";
                    $newData['DOKRESNO'] = trim(fgets(STDIN));
                    echo "tgl SPPB (dd-mm-yyyy): ";
                    $newData['DOKRESTG'] = trim(fgets(STDIN));

                    // add em
                    $updated[] = $newData;
                }
                // fclose($fp);

                // now execute the update
                $cnt = count($updated);
                $i = 0;
                $success = 0;
                $error = 0;

                $stmt=$db->prepare("UPDATE tblPibRes SET DOKRESNO=:DOKRESNO, DOKRESTG=:DOKRESTG WHERE CAR=:CAR");

                foreach ($updated as $update) {
                    try {
                        $stmt->execute($update);
                        $success++;
                    } catch (PDOException $e) {
                        //throw $th;
                        $error++;
                        echo "\nError: {$e->getMessage()}\n";
                    }
                    ++$i;
                    echo "Executing update...{$i} of {$cnt}, success({$success}), error({$error})\r";
                }
                
            }
        }
    } catch (\Exception $e) {
        echo "Error: {$e->getMessage()}\n";
    }
}