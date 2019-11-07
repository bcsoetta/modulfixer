<?php
include 'include.php';


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