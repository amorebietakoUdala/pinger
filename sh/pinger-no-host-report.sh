#!/bin/bash

NETFOLDER=/var/www/SF7/pinger/
cd $NETFOLDER
$NETFOLDER/bin/console app:pinger-nohost-report &>> $NETFOLDER/var/log/no-hosts.log
