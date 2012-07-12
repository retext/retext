#!/bin/bash

ENV=$1
USER=$2
if [[ $USER == "" ]];
then
    USER=`whoami`
fi

chown -R $USER: app/cache/$ENV
if [ ! -d app/logs ]; then mkdir app/logs; fi;
if [ ! -f app/logs/$ENV.log ]; then touch app/logs/$ENV.log; fi;
chown $USER: app/logs/$ENV.log
