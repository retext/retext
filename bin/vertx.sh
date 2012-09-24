#!/bin/bash

VERTX_BIN=/opt/vert.x-1.2.3.final/bin/vertx
CP="-cp vert.x/jyson-1.0.2.jar"
JYTHON_HOME=/opt/jython


case "$1" in
    start)
        export JYTHON_HOME
        $VERTX_BIN run vert.x/router.py -cluster -cluster-port 25500 $CP &
        echo $! > vert.x/router.py.pid
        $VERTX_BIN run vert.x/userregister.py -cluster -cluster-port 25501 $CP &
        echo $! > vert.x/userregister.py.pid
        ;;
    stop)
        kill `cat vert.x/router.py.pid` `cat vert.x/userregister.py.pid`
        ;;
    restart)
        $0 stop
        $0 start
        ;;
    *)
        echo "usage: $0 {start|stop|restart}"
        ;;
esac

