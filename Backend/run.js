var express = require('express');
var app = express();
var login = require('./user/login');

app.post('/login', function (req, res) {
   var result = login.login(req.userName,req.pas);
   res.end(result);
})

var server = app.listen(8081, function () {

  var host = server.address().address
  var port = server.address().port

  console.log("Server started!");
})