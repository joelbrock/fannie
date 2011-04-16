#!/bin/sh

mysqldump -uroot -h 192.168.123.99 --add-drop-database is4c_op > /tmp/is4c_op.sql || {
		echo Can't read tart or write to trunk
		exit
}
echo Dumped database to local /tmp directory...

mysql -uroot -peng@ge -e "DROP DATABASE tmp_op; CREATE DATABASE tmp_op;" || {
		echo Couldn't mess with databases
		exit
}
echo Flushed the tmp database.  Ready for new data...

mysql -uroot -peng@ge tmp_op < /tmp/is4c_op.sql || {
		echo Something broke!
		exit
}
echo Test run worked.  Lets do the transfer for real now.

mysql -uroot -peng@ge -s -e "DROP DATABASE is4c_op; CREATE DATABASE is4c_op;" || {
		echo Uh-oh....shit!
		exit
}
echo Flushed is4c_op...

mysql -uroot -peng@ge -s is4c_op < /tmp/is4c_op.sql || {
		echo FUCK!!!
		exit
}
echo Imported the stuff.  Got it!
echo Done.
