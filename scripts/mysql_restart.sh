#! /bin/bash

mysqladmin -uroot -peng@ge shutdown
mysqld_safe &
