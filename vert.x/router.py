import vertx
from com.xhaus.jyson import JysonCodec as json
from core.event_bus import EventBus
from exceptions import Exception

server = vertx.create_http_server()

class BadRequest(Exception):
    pass

@server.request_handler
def handle(request):
    
    @request.body_handler 
    def body_handler(body):
        args = {}
        if body.length > 0:
            args = json.loads(body.to_string())
            
        if request.path == "/register":
            if 'email' not in args:
                raise BadRequest('Missing email.')
            registerUser(request, args['email'])
        request.response.end(json.dumps({'error': False, 'code': 0, 'message': 'OK'}))
    
def registerUser(request, email):
    """Wird aufgerufen, wenn sich ein neuer Nutzer registriert"""
    EventBus.send('user.register', email)  
    
server.listen(8080)


