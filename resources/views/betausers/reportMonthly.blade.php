@extends('betausers.master')
@section('content')
<div class="row"  style="max-width: 90%;">
	<div class="column small-4">
		{{$a_month_ago}} Total:
	</div>
	<div class="column small-2 end">
		${{$today_total}}
	</div>
</div>
<div class="row"  style="max-width: 90%;">
	<div class="column small-4">
		{{$two_month_ago}} Total:
	</div>
	<div class="column small-2 end">
		${{$yesterday_total}}
	</div>
</div>
<div class="row"  style="max-width: 90%;">
	<div class="column small-4">
		{{$three_month_ago}} Total:
	</div>
	<div class="column small-2 end">
		${{$this_month_total}}
	</div>
</div>
<div class="row" style="max-width: 90%;">
	<div class="column large-4 small-12" style="padding-top: 1em;">
		<div class="row">
			<div class="column small-12 text-center" style="color: Tomato;">{{$a_month_ago}}'s Stat</div>
		</div>
		<div class="row">
			<div class="column small-3">Source</div>
			<div class="column small-3">Clicks</div>
			<div class="column small-3">Conversions</div>
			<div class="column small-3">Value</div>
		</div>
		@foreach($today as $key)
			<div class="row">

				<div class="column small-3">{{$key->Source}}</div>
				<div class="column small-3">{{$key->Clicks}}</div>
				<div class="column small-3">{{$key->Conversions}}</div>
				<div class="column small-3">@if($key->Dollar_Value != "--")${{number_format($key->Dollar_Value, 2)}}@else{{$key->Dollar_Value}}@endif</div>
			</div>
		@endforeach
	</div>
	<div class="column large-4 small-12" style="padding-top: 1em;">
		<div class="row">
			<div class="column small-12 text-center" style="color: Tomato;">{{$two_month_ago}}'s Stat</div>
		</div>
		<div class="row">
			<div class="column small-3">Source</div>
			<div class="column small-3">Clicks</div>
			<div class="column small-3">Conversions</div>
			<div class="column small-3">Value</div>
		</div>
		@foreach($yesterday as $key)
			<div class="row">

				<div class="column small-3">{{$key->Source}}</div>
				<div class="column small-3">{{$key->Clicks}}</div>
				<div class="column small-3">{{$key->Conversions}}</div>
				<div class="column small-3">@if($key->Dollar_Value != "--")${{number_format($key->Dollar_Value, 2)}}@else{{$key->Dollar_Value}}@endif</div>
			</div>
		@endforeach
	</div>
	<div class="column large-4 small-12" style="padding-top: 1em;">
		<div class="row">
			<div class="column small-12 text-center" style="color: Tomato;">{{$three_month_ago}}'s Stat</div>
		</div>
		<div class="row">
			<div class="column small-3">Source</div>
			<div class="column small-3">Clicks</div>
			<div class="column small-3">Conversions</div>
			<div class="column small-3">Value</div>
		</div>
		@foreach($this_month as $key)
			<div class="row">

				<div class="column small-3">{{$key->Source}}</div>
				<div class="column small-3">{{$key->Clicks}}</div>
				<div class="column small-3">{{$key->Conversions}}</div>
				<div class="column small-3">@if($key->Dollar_Value != "--")${{number_format($key->Dollar_Value, 2)}}@else{{$key->Dollar_Value}}@endif</div>
			</div>
		@endforeach
	</div>
</div>

<div class="row"  style="max-width: 90%;margin-top: 50px;">
	<div class="column small-12">
		********************************************************
	</div>
</div>
<div class="row"  style="max-width: 90%;margin-top: 50px;">
	<div class="column small-4">
		{{$a_month_ago}} appended:
	</div>
	<?php $cnt = 0; 
		$appended_today_cnt =  count($appended_today) - 1;
	?>
	@foreach($appended_today as $key)
	<div class="column small-2 @if($cnt == $appended_today_cnt) end @endif">
		@if(!isset($key->in_college))
			Not Set {{$key->cnt}}
		@elseif($key->in_college == 0)
			HS {{$key->cnt}}
		@elseif($key->in_college == 1)
			College {{$key->cnt}}
		@endif
	</div>
	<?php $cnt++; ?>
	@endforeach
</div>
<div class="row"  style="max-width: 90%;">
	<div class="column small-4">
		{{$two_month_ago}} appended:
	</div>
	<?php $cnt = 0; 
	      $appended_yesterday_cnt =  count($appended_yesterday) - 1;
	?>
	@foreach($appended_yesterday as $key)
	<div class="column small-2 @if($cnt == $appended_yesterday_cnt) end @endif">
		@if(!isset($key->in_college))
			Not Set {{$key->cnt}}
		@elseif($key->in_college == 0)
			HS {{$key->cnt}}
		@elseif($key->in_college == 1)
			College {{$key->cnt}}
		@endif
	</div>
	<?php $cnt++; ?>
	@endforeach
</div>
<div class="row"  style="max-width: 90%;margin-bottom: : 10px;">
	<div class="column small-4">
		{{$three_month_ago}} appended:
	</div>
	<?php $cnt = 0; 
		  $appended_this_month_cnt =  count($appended_this_month) - 1;
	?>
	@foreach($appended_this_month as $key)
	<div class="column small-2 @if($cnt == $appended_this_month_cnt) end @endif">
		@if(!isset($key->in_college))
			Not Set {{$key->cnt}}
		@elseif($key->in_college == 0)
			HS {{$key->cnt}}
		@elseif($key->in_college == 1)
			College {{$key->cnt}}
		@endif
	</div>
	<?php $cnt++; ?>
	@endforeach
</div>

<div class="row"  style="max-width: 90%;margin-top: 50px;">
	<div class="column small-12">
		********************************************************
	</div>
</div>
<div class="row"  style="max-width: 90%;margin-top: 50px;">
	<div class="column small-4">
		{{$a_month_ago}} clicks:
	</div>
	<div class="column small-2 end">
		{{$today_clicks}}
	</div>
</div>
<div class="row"  style="max-width: 90%;">
	<div class="column small-4">
		{{$two_month_ago}} clicks:
	</div>
	<div class="column small-2 end">
		{{$yesterday_clicks}}
	</div>
</div>
<div class="row"  style="max-width: 90%;margin-bottom: : 10px;">
	<div class="column small-4">
		{{$three_month_ago}} clicks:
	</div>
	<div class="column small-2 end">
		{{$this_month_clicks}}
	</div>
</div>
@stop