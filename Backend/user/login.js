var mysql = require('mysql');
var config = require('config');

function login(username, pas)
{	
	var dbConfig = 	config.get('database');
    var con = mysql.createConnection({
		host: dbConfig.host,
		user: dbConfig.user,
		password: dbConfig.password
		});

	con.connect(function(err) {
	if (err) throw err;
	console.log("Connected!");
	});
	return "cool";
}

function validate(token)
{
	return ;
}

module.exports.login = login;