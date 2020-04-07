<!DOCTYPE html>
<html>
  <head>
    <title>Hello Client Monkey 5</title>
    <script type="text/javascript"
      src="//media.twiliocdn.com/sdk/js/client/v1.3/twilio.min.js"></script>
    <script type="text/javascript"
      src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js">
    </script>
    <link href="//static0.twilio.com/resources/quickstart/client.css"
      type="text/css" rel="stylesheet" />
    <script type="text/javascript">

      Twilio.Device.setup("{{$token or ''}}");

      Twilio.Device.ready(function (device) {
        $("#log").text("Client {{$client_name or ''}} is ready");
      });

      Twilio.Device.error(function (error) {
        $("#log").text("Error: " + error.message);
      });

      Twilio.Device.connect(function (conn) {
        $("#log").text("Successfully established call");
      });

      Twilio.Device.disconnect(function (conn) {
        $("#log").text("Call ended");
      });

      Twilio.Device.incoming(function (conn) {
        $("#log").text("Incoming connection from " + conn.parameters.From);
        // accept the incoming connection and start two-way audio
        conn.accept();
      });

      function call() {
        // get the phone number to connect the call to
        params = {"PhoneNumber": $("#number").val()};
        var conn = Twilio.Device.connect(params);
        console.log(conn);
        var params = conn.parameters;
        console.log(params);
        var sid = params.CallSid;
        console.log(sid);
      }

      function hangup() {
        Twilio.Device.disconnectAll();
      }
    </script>
  </head>
  <body>
    <button class="call" onclick="call();">
      Call
    </button>

    <button class="hangup" onclick="hangup();">
      Hangup
    </button>
        
    <input type="text" id="number" name="number"
      placeholder="Enter a phone number or client to call"/>

    <div id="log">Loading pigeons...</div>
  </body>
</html>
