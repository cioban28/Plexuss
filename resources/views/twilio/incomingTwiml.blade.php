<Response>
    <Dial callerId="{{$fromPhone or ''}}" record="record-from-answer-dual" recordingStatusCallback="{{$currentUrlForTwilio}}/phone/recordCallBack">
        <Client>{{$client_name}}</Client>
    </Dial>
</Response>