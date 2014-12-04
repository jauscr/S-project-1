 function SooundLive(){
	var webSocket;
	var session;
	var channels = [];

	this.connected = function(){
		return session ? true : false;
	};

	this.addChannel = function(uri, fun){
		channels[uri] = fun;
		if(session)
			session.subscribe(uri, fun);
	};

	this.removeChannel = function(uri){
		if(session)
			session.unsubscribe(uri);
		delete channels[uri];
	};

	this.connect = function(fun){
		// connect to WAMP server
		ab.connect(_RATCHET_URI,
			// WAMP session was established
			function (sess) {

				session = sess;
				//console.log("Connected to " + _RATCHET_URI);

				// subscribe to topic, providing an event handler
				if(channels){
	          		for (var uri in channels){
	          			session.subscribe(uri, channels[uri]);
	          		}
				}

				if(fun)
					fun();
			},

			// WAMP session is gone
			function (code, reason) {
				session = null;
				console.log("Connection lost (" + reason + ")");
			},
			{skipSubprotocolCheck : true}
		);
	};

	this.send = function(uri, data){
		session.publish(uri, data);
	};
};

var sooundLive = new SooundLive();