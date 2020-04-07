<Response>
    <Dial callerId="{{$fromPhone or ''}}" record="record-from-answer-dual" recordingStatusCallback="{{$currentUrlForTwilio}}/phone/recordCallBack">
        <Number>{{$toPhone or ''}}</Number>
    </Dial>
</Response>