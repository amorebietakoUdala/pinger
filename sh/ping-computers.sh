#!/bin/bash

NETFOLDER=/var/www/SF7/pinger/
cd $NETFOLDER
$NETFOLDER/bin/console app:ping-computers &>> $NETFOLDER/var/log/ping-computers.log
