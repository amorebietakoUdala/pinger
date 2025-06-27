#!/bin/bash

NETFOLDER=/var/www/SF7/pinger/
cd $NETFOLDER
$NETFOLDER/bin/console app:unifi-accesspoint-report &>> $NETFOLDER/var/log/access-point-daily-report.log
