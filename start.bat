@echo off
echo deleting results...
del result*
echo deleting clean.mdb
del clean.mdb
echo deleting error.log
del error.log
echo reinstantiate clean.mdb
copy dbPIB-clean.mdb clean.mdb
