#!/bin/sh

date="date"

mysqldump -uroot -h 192.168.123.99 --add-drop-database is4c_log > /tmp/is4c_log.sql || {
		echo "Can't read tart or write to trunk"
		exit
}
echo "Dumped database to local /tmp directory..."

mysql -uroot -peng@ge -e "DROP DATABASE tmp_log; CREATE DATABASE tmp_log;" || {
		echo "Couldn't mess with databases"
		exit
}
echo "Flushed the tmp database.  Ready for new data..."

mysql -uroot -peng@ge -f tmp_log < /tmp/is4c_log.sql || {
		echo "Something broke!"
		exit
}
echo "Test run worked.  Lets do the transfer for real now."

mysql -uroot -peng@ge -e "DROP DATABASE is4c_log; CREATE DATABASE is4c_log;" || {
		echo "Uh-oh....shit!"
		exit
}
echo "Flushed is4c_log..."

mysql -uroot -peng@ge -f is4c_log < /tmp/is4c_log.sql || {
		echo "FUCK!!!"
		exit
}
echo "Imported the stuff.  Got it!"
echo "Done. -- '$(date)'"
