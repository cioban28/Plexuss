<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FiveOne Socket.io</title>
</head>
<body>
<h1>New users:</h1>
<ul>
    <li>dadasdasda</li>
</ul>

<button onclick="load();">Click me</button>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src = "https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.3/socket.io.js"></script>

<script>
    // var socket = io.connect('http://localhost:6379/');

    // new Vue({
    //     el: 'body',

    //     data: {
    //         users: []
    //     },

    //     ready: function(){
    //         socket.on('test-channel:UserSignedup', function(data){
    //             this.users.push(data.username);
    //         }).bind(this);
    //     }
    // });
var socket = io.connect('http://localhost:6379/');
$(document).ready(function(){
    socket.on('test-channel:UserSignedup', function(data){
        console.log(data);
        // this.users.push(data.username);
    });
});

function load(){
    console.log('heereerere');
    socket.emit('test-channel:UserSignedup', '{"user_id":2222222222,"socket":null}');
}
</script>

</body>
</html>