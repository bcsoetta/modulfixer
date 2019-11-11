<?php
include 'include.php';

if ($argc != 3) {
    echo "php removedataolderthanyear.php [mdb] [year]\n";
    echo "remove all PIB data from [mdb] with year\n";
    echo "older than [year].\n";
    echo "for thinning purpose...\n";
} else {
    // query all data whom fits all requirement
    $tables = [
        'tblKomLog',
        'tblPibCon',
        'tblPibConR',
        'tblPibDok',
        'tblPibDtl',
        'tblPibDtlDok',
        // 'tblPibDtlFas',
        // 'tblPibDtlLartas',
        'tblPibDtlSpekKhusus',
        'tblPibDtlVD',
        // 'tblPibFas',
        'tblPibHdr',
        'tblPibKendaraan',
        'tblPibKms',
        'tblPibNpt',
        'tblPibNTB',
        'tblPibPgt',
        'tblPibRes',
        'tblPibResBill',
        'tblPibResNPBL',
        'tblPibResNPD',
        'tblPibTrf'
    ];

    // loop all over em all, and do shit
    try {
        echo "Opening db...";
        $db = openDB($argv[1]);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $year = $argv[2];

        echo "Removing data from tables whose year is < {$year}...\n";
        // specific case for komlog
        foreach ($tables as $tbl) {
            echo "Table: {$tbl}...";
            if ($tbl == 'tblKomLog') {
                $db->query("DELETE FROM tblKomLog WHERE YEAR(KomTg) < {$year}");
            } else {
                // use another query
                $db->query("DELETE FROM {$tbl} WHERE Mid(CAR, 13, 4) < {$year}");
            }
            echo "done!\n";
        }
    } catch (\Exception $e) {
        echo "Error : {$e->getMessage()}\n";
    }
}