var express = require('express');
var app = express();
var http = require('http').Server(app);
var io = require('socket.io')(http);
var path = require('path');
const shell = require('shelljs');

app.use(express.static(path.join(__dirname, 'public', 'css')));

//-----------------namespace socket--------------------------//
var controlIo = io.of('/control');
var indexIo = io.of('/index');

//-------------------------ajout du controlle de l'utilisateur----------------------//
var controlIo = io.of('/control');
controlIo.on('connection', function() {
    console.log('test')
})
app.get("/control", function(req, res) {
    res.sendFile(__dirname + '/control.html');


})

//-------------------------ajout d'un utilisateur---------------------//
app.get("/index", function(req, res) {
    if (typeof req.param('pseudo') != 'undefined' && typeof req.param('mac') != 'undefined' && typeof req.param('ip') != 'undefined') {
        var pseudo = req.param('pseudo');
        var mac = req.param('mac');
        var ip = req.param('ip');
        var date = req.param('date');
        data = [pseudo, mac, ip, date];
        console.log(data);
        indexIo.emit('connected', data);
    }
    res.sendFile(__dirname + '/index.html');
})

//fonction suppression utilisateur
var delUser = function deleteUser(toDel) {
    var db = dbconnect();
    db.connect(function(err) {
        db.query("DELETE FROM connected WHERE mac = '" + toDel + "'");
    });
    indexIo.emit('deconnex', toDel);
    shell.exec("sudo /home/raph35/Documents/Projets/findetudel3misa/gitHub/captivePortal/script_bash/./removeUser.sh " + toDel);
}

indexIo.on('connection', function(socket) {
    //------génération des utilisateur en ligne via mysql------//
    console.log('user connected');
    data = getUser(socket);

    ///supression en cas d'utilisateur indésirable
    socket.on('delete', delUser);

});







//--------------------------écoute dans 8080------------//
http.listen(8080, function(req, res) {
    console.log("server running on 8080");
})

//////---------------MYSQL----------------///////
function dbconnect() {
    var mysql = require('mysql');
    var connection = mysql.createConnection({
        host: 'localhost',
        user: 'root',
        password: '',
        database: 'portailcaptif'
    });

    return connection
}

function insertUser() {
    var db = dbconnect();

    db.connect();
    var email = "mail";
    var mdp = "123";
    var pseudo = "utilisateur";
    var data = [pseudo, email, mdp];
    db.query('INSERT INTO user SET pseudo=?, email=?, mdp=?', data, (err, user, field) => {

    });
}

function getUser(socket) {
    var db = dbconnect();
    var data;
    db.connect(function(err) {
        if (err) throw err;
        db.query('SELECT * FROM connected', (err, result, field) => {
            if (err) throw err;
            //console.log('>> results: ', result );
            var string = JSON.stringify(result);
            //console.log('>> string: ', string );
            var json = JSON.parse(string);
            //console.log('>> json: ', json)
            //console.log(result);
            socket.emit('select', json);
        });
    });
}