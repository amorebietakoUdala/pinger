#!/bin/bash

NETFOLDER=/var/www/SF7/pinger/
cd $NETFOLDER
$NETFOLDER/bin/console  app:ping-unifi-access-points &>> $NETFOLDER/var/log/access-point-pinger.log
