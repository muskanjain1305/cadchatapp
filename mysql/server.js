// use express
var express = require("express");

// create instance of express
var app = express();

// use http with instance of express
var http = require("http").createServer(app);

// create socket instance with http
var io = require("socket.io")(http, {
    allowEIO3: true
});

// add listener for new connection
io.on("connection", function(socket) {
    // this is socket for each user
    console.log("User connected", socket.id);

    socket.on("new_message", function(data) {
        console.log("Client says", data);
    });
});

app.get("/", function(request, result) {
    result.send("Hello World !");
});

var port = 3000;
http.listen(port, function() {
    console.log("Listening to port " + port);
});