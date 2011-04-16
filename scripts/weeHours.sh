#!/bin/bash

date="date -I"

mysql -uroot -peng@ge < /pos/fannie/scripts/batchUpdate

if [ $? == 0 ]
then 
echo "`$date` - Update products table with new sales batch data." >> /pos/fannie/logs/nightlylog.txt
else
echo "`$date` - ERROR - Sales batch update failed." >> /pos/fannie/logs/nightlylog.txt
fi

mysql -uroot -peng@ge is4c_log -e "INSERT INTO dlog_"$(date +%Y)" (SELECT * FROM dtransactions)"

if [ $? == 0 ]
then
mysql -uroot -peng@ge is4c_log -e "TRUNCATE TABLE dtransactions"
echo "`$date` - dtransactions archived and truncated." >> /pos/fannie/logs/nightlylog.txt
else
mysqldump -uroot -peng@geis4c_log dtransactions > /pos/archives/dtransactions_`$date`.sql
mysqldump -uroot -peng@ge is4c_log dlog_'$year' > /pos/archives/dlog_`$(date +%Y)`_`$date`.sql
echo "`$date` - ERROR - Archival failed. Backed up transaction tables." >> /pos/fannie/logs/nightlylog.txt
fi

mysqldump -uroot -peng@ge is4c_op custdata products | mysql -u root -h 192.168.0.201 opdata

if [ $? == 0 ]
then
echo "`$date` - Sync to lane01 successful." >> /pos/fannie/logs/nightlylog.txt
else
echo "`$date` - ERROR - Sync to lane01 failed." >> /pos/fannie/logs/nightlylog.txt
fi

mysqldump -uroot -peng@ge is4c_op custdata products | mysql -u root -h 192.168.0.202 opdata

if [ $? == 0 ]
then
echo "`$date` - Sync to lane02 successful." >> /pos/fannie/logs/nightlylog.txt
else
echo "`$date` - ERROR - Sync to lane02 failed." >> /pos/fannie/logs/nightlylog.txt
fi

# mysqldump -uroot -peng@ge is4c_op custdata products | mysql -u root -h 192.168.0.203 opdata
# 
# if [ $? == 0 ]
# then
# echo "`$date` - Sync to lane03 successful." >> /pos/fannie/logs/nightlylog.txt
# else
# echo "`$date` - ERROR - Sync to lane03 failed." >> /pos/fannie/logs/nightlylog.txt
# fi
