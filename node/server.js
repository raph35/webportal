//server express
var express = require('express');
var app = express();
//server http par express
var http = require('http').Server(app);
//socket.io
var io = require('socket.io')(http);
//path
var path = require('path');
//execution commande shell
const shell = require('shelljs');
//variable session par express
var session = require('express-session')({
    secret: "my-secret",
    resave: true,
    saveUninitialized: true
});
//pont entre les variable session de expres ver socket 
var sharedsession = require("express-socket.io-session");
//BodyParser
var bodyParser = require("body-parser");
const { send } = require('process');
const { resolve } = require('path');
const { rejects } = require('assert');

//utilisation du midlware css
app.use(express.static(path.join(__dirname, 'public', 'css')));
//-------------------appelle du midlware session dans express ::::express session
app.use(session);

//-------------------midlware bodyparser
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

//-----------------namespace socket--------------------------//
var controlIo = io.of('/control');
var indexIo = io.of('/index');
//----------------midlware session dans /control
controlIo.use(sharedsession(session, {
    autoSave: true
}))

app.post("/control", function(req, res) {
    const sess = req.session;
    //------------------------reception de l'utilisateur--------------------------------//
    if (typeof req.body.pseudo != 'undefined' && typeof req.body.mac != 'undefined' && typeof req.body.ip != 'undefined' && req.body.token == '03246') {
        sess.pseudo = req.body.pseudo;
        sess.mac = req.body.mac;
        sess.ip = req.body.ip;
        //sess.heure = req.body.heure;
        getUserTime(sess.pseudo).then(function(results){
            sess.heure=results[0];
            if(results[1]==0 || results[1]=='0')
            {
                res.redirect(301, 'http://10.42.0.1:81/');
            }else{
                data = [sess.pseudo, sess.mac, sess.ip, sess.heure];

                //emission dans la page admin
                indexIo.emit('connected', data);
                //controlIo.emit('session', sess);
                console.log(data);
                res.sendFile(__dirname + '/control.html');
            }
        }).catch(function(err){
            console.log('promise rejection error : '+err)
        })
        
    } else {
        res.redirect(301, 'http://10.42.0.1:81/');
    }
})


//-------------------------ajout du control de l'utilisateur----------------------//
controlIo.on('connection', function(socket) {
  
    //récupération des information de l'utilisateur depuis socket du route
    console.log('user connected:' + socket.handshake.session.pseudo)
    var pseudo = socket.handshake.session.pseudo,
        mac = socket.handshake.session.mac,
        ip = socket.handshake.session.ip,
        heure = socket.handshake.session.heure;
        if(heure=='deleted'){
            socket.emit('notEnter','notEnter');
        }
        console.log('tonga any am user:'+heure);
        //autorisation
        shell.exec("sudo /usr/local/lib/captiveportal/./addUser.sh " + mac + " " + ip)
    
    data = [pseudo, mac, ip, heure];
    socket.emit('about', data)

    var time;
    if (heure != "")
        var time = heure;
    //------------------------Chrono---------------------------//
    var timeout;

    function decrementer() {
        if (time >= 0) {
            if (time == 0) {
                socket.emit('noTime', 'Temps écoulé');
                socket.emit('chrono', time);
                socket.disconnect();
            }
            time--;
            //emission dans l'utilisateur
            socket.emit('chrono', time);
            //emission dans l'admin
            if (time > 0) {
                data = [pseudo, mac, ip, time]
                indexIo.emit('connected', data);
            }
            timeout = setTimeout(decrementer, 1000);
        }
    }
    setTimeout(decrementer, 1000);

    //---------------------deconnection par l'interface admin
    socket.on('delete', function(mac) {
        controlIo.emit('delete', mac);
    })

    //---------------------deconnection dans l'interface admin
    socket.on('deconnection', function(mac) {
        socket.disconnect();
    })

    //-------------------deconnection de la page
    socket.on('disconnect', function() {
        clearTimeout(timeout);
        indexIo.emit('deconnex', mac);
        console.log(time);
        delUser(mac, time);
        console.log('To update Mysql:' + pseudo);
        socket.handshake.session.heure='deleted';
        // socket.handshake.session.heure=5000;
        socket.handshake.session.save();
    })
})


//-------------------------ajout d'un utilisateur---------------------//
app.post("/index", function(req, res) {
    /*if (typeof req.param('pseudo') != 'undefined' && typeof req.param('mac') != 'undefined' && typeof req.param('ip') != 'undefined') {
        var pseudo = req.param('pseudo');
        var mac = req.param('mac');
        var ip = req.param('ip');
        var heure = req.param('heure');
        data = [pseudo, mac, ip, heure];
        console.log(data);
        indexIo.emit('connected', data);
    }*/
    if (req.body.token == '03246')
        res.sendFile(__dirname + '/index.html');
    else {
        res.redirect(301, 'http://10.42.0.1:81/');
    }
})

//fonction suppression utilisateur
/*var delUser = function deleteUser(toDel) {
        var db = dbconnect();
        db.connect(function(err) {
            db.query("DELETE FROM connected WHERE mac = '" + toDel + "'");
        });
        indexIo.emit('deconnex', toDel);
        shell.exec("sudo /home/raph35/Documents/Projets/findetudel3misa/gitHub/captivePortal/script_bash/./removeUser.sh " + toDel);
    }
    */
//deconnection d'un utilisateur
var delUser = function deleteUser(mac, time) {
    var db = dbconnect();
    db.connect(function(err) {
        //db.query("UPDATE $this->table SET heure='" + toDel[1] + "',isConnected='0' WHERE mac='" + toDel[0] + "'");
        db.query("UPDATE connected SET heure=" + time + ",isConnected=0 WHERE mac='" + mac + "' AND isConnected=1;",function(err,rows){
            if(err){
                console.log('error query:'+err.stack)
            }
        });
    });
    controlIo.emit('delete', mac);
    indexIo.emit('deconnex', mac);
    shell.exec("sudo /usr/local/lib/captiveportal/removeUser.sh " + mac);
}
var deleteUserMac = function(mac) {
    controlIo.emit('delete', mac);
}

indexIo.on('connection', function(socket) {
    //------génération des utilisateur en ligne via mysql------//
    console.log('user connected');
    data = getUser(socket);

    ///supression en cas d'utilisateur indésirable
    socket.on('delete', deleteUserMac);

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
        user: 'admin',
        password: 'admin#portal',
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

function getUserTime(pseudo){
    
    return new Promise((resolve,reject)=>{
        var db = dbconnect();
        db.query("SELECT * FROM connected WHERE pseudo= ?",[pseudo],(err,result)=>{
            if(err){ reject(new Error(err)) }
            else {
                console.log("hahah:"+result[0].isConnected)
                resolve([result[0].heure,result[0].isConnected])
            }
        })
    })
}

function getUser(socket) {
    var db = dbconnect();
    var data;
    db.connect(function(err) {
        if (err) throw err;
        db.query("SELECT * FROM connected WHERE isConnected=1", (err, result, field) => {
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