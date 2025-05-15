#!/bin/bash

NETFOLDER=/var/www/SF7/pinger/
cd $NETFOLDER
$NETFOLDER/bin/console app:pinger-report &>> $NETFOLDER/var/log/daily-report.log
