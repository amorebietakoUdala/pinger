#!/bin/bash

NETFOLDER=/var/www/SF7/pinger/
cd $NETFOLDER
$NETFOLDER/bin/console app:pinger-duplicate-host &>> $NETFOLDER/var/log/duplicate-hosts.log
