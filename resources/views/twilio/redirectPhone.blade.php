<Response>
    <Dial callerId="{{$fromPhone or ''}}" timeout="10" record="record-from-answer-dual" recordingStatusCallback="{{$currentUrlForTwilio}}/phone/recordCallBack">{{$phone or ''}}</Dial>
</Response>