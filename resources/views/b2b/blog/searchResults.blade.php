

<div class="f-bold mb15" >We've found <span class="total-count">{{$count or '' }}</span> results for <span class="term f-italic"> {{$term or 'your search'}}...</span></div>


@foreach($results as $result)
<div class="row result-row seemore-btn"  data-id="{{$result->id}}">
	<div class="column small-2">
		@if(isset($result->img_sm)  && $result->img_sm != '')
			<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/{{$result->img_sm or ''}}" />
		@else
			<img src="/images/no_photo.jpg" />
		@endif
	</div>
	<div class="column small-10 mt20">
		{{ $result->title or ''}}
		<div class="timeago-txt">{{$result->timeAgo or ''}}</div>
	</div>

</div>
@endforeach