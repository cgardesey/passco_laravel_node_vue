let app = require('express')();
let server = require('http').Server(app);
let io = require('socket.io')(server);
let redis = require('redis');
let axios = require('axios');
let { Timer } = require('easytimer.js');

const Path = 'http://localhost:5000/api';

let timers = [];

server.listen(6999);
io.on('connection', function(socket){
    console.log('Starting');
    let subClient = redis.createClient();
    let pubClient = redis.createClient();


    subClient.subscribe('transaction-channel');
    subClient.on("message", function(channel, message){
        message = JSON.parse(message);
        console.log('transaction-response', 'response arrived');
        socket.emit('transaction-response_' + message.data.transaction.user_id.toString(), message.data.transaction.message);
    });


    socket.on('user-connected', function (data) {
        // we store the username in the socket session for this client
        let user_id = data.user_id
        socket.username = user_id;

        axios.patch(Path + '/users/' + user_id, {connected: 1})
            .then(response => {
                console.log('User with id ' + user_id, ' Connected');
            })
            .catch(error => {
                console.log('Error updating user connected for user with id ' + user_id , error.response.data.error);
            });
    });

    socket.on('start-timer', function (data) {
        let user_id =  data.user_id;

        axios.get(Path + '/users/' + user_id)
            .then(response => {
                console.log(timers[user_id] != null);
                if (timers[user_id] != null) {
                    timers[user_id].stop();
                }
                timers[user_id] = new Timer();
                timers[user_id].start({
                    countdown: true,
                    startValues: {seconds: response.data.timeremaining},
                    callback: function (timer) {

                        let seconds_remaining = timer.getTotalTimeValues().seconds;
                        console.log(seconds_remaining);
                        if (seconds_remaining === 0) {
                            timers[user_id].stop();
                            io.sockets.emit('no-time_' + user_id.toString());
                            axios.patch(Path + '/users/' + user_id, {timeremaining: 0})
                                .then(response => {
                                })
                                .catch(error => {
                                    console.log('Error updating user time remaining upon time exhausted for user with id ' + user_id, error.response.data.error);
                                });
                        }
                        else {
                            io.sockets.emit(user_id.toString(), seconds_remaining);
                            if (seconds_remaining % 10 === 0) {
                                axios.patch(Path + '/users/' + user_id, {timeremaining: seconds_remaining})
                                    .then(response => {
                                        // console.log('10 secs elapse update for user with id ' + user_id);
                                    })
                                    .catch(error => {
                                        console.log('Error updating user time remaining upon 10 secs elapse for user with id ' + user_id, error.response.data.error);
                                    });
                            }
                        }
                    }});
            })
            .catch(error => {
                console.log('Error Retrieving User time remaining upon timer start for user with id ' + user_id, error);
            });


    });

    socket.on('stop-timer', function (data) {
        console.log('timer stoped');
        let user_id =  data.user_id;

        // timers[user_id] = null;
        axios.patch(Path + '/users/' + user_id, {timeremaining: timers[user_id].getTotalTimeValues().seconds})
            .then(response => {
                timers[user_id].stop();
            })
            .catch(error => {
                console.log('Error updating user time remaining upon timer pause for user with id ' + user_id, error.response.data.error);
            });
    });

    socket.on('disconnect', function(){
        // subClient.quit();

        let user_id = socket.username;

        if (user_id !== undefined && timers[user_id] !== undefined){
            console.log('Upon disconnection, user with id ' + user_id + ' has ' + timers[user_id].getTotalTimeValues().seconds + ' secs remaining');
            axios.patch(Path + '/users/' + user_id, {connected: 0, timeremaining: timers[user_id].getTotalTimeValues().seconds})
                .then(response => {
                    timers[user_id].stop();
                })
                .catch(error => {
                    console.log('Error updating user disconnected and user timeremaining upon user disconnection for user with id ' + user_id, error.response.data.error);
                });
        }
    });
});
