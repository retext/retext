from core.event_bus import EventBus
from time import sleep

def user_register(message):
    print"Got message body %s" % message.body
    # Wait
    sleep(1)
    print "Mail sent"

id = EventBus.register_handler('user.register', False, user_register)
