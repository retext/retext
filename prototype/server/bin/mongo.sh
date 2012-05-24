#!/bin/bash

WD=`dirname $0`/..
ABSPATH=`cd $WD; pwd`
PID=$ABSPATH/mongodb/mongod.pid

case "$1" in
    start)
        if [ -f $PID ]; then
            [ -f $PID ] && kill -0 `cat $PID` >/dev/null 2>&1
            if [ $? == 0 ]; then
                echo "Already running."
                exit 2
            fi
        fi
        mongod --fork --pidfilepath=$PID --logpath=$ABSPATH/mongodb/mongod.log --dbpath=$ABSPATH/mongodb/
        ;;
    stop)
        [ -f $PID ] && kill `cat $PID` >/dev/null 2>&1
        if [ $? -gt 0 ]; then
            echo "Failed to stop mongod."
            exit 1
        else
            rm -f $PID
            echo "Stopped."
        fi
        ;;
    *)
        echo "usage: $0 {start|stop}"
        ;;
esac

