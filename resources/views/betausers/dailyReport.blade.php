@extends('betausers.master')
@section('content')
<div class="row"  style="max-width: 95%;">
	<div class="column small-4">
		Today Total:
	</div>
	<div class="column small-2 end">
		${{$today_total}}
	</div>
</div>
<div class="row"  style="max-width: 95%;">
	<div class="column small-4">
		Yesterday Total:
	</div>
	<div class="column small-2 end">
		${{$yesterday_total}}
	</div>
</div>
<div class="row"  style="max-width: 95%;">
	<div class="column small-4">
		This month Total:
	</div>
	<div class="column small-2 end">
		${{$this_month_total}}
	</div>
</div>
<div class="row" style="max-width: 95%;">
	<div class="column large-4 small-12" style="padding-top: 1em;">
		<div class="row">
			<div class="column small-12 text-center" style="color: Tomato;">Today's Stats</div>
		</div>
		<div class="row">
			<div class="column small-4">Source</div>
			<div class="column small-3 text-right">Clicks</div>
			<div class="column small-3 text-right">Conversions</div>
			<div class="column small-2 text-left">Value</div>
		</div>

		@if(isset($today['top']))
			@foreach($today['top'] as $key => $value)
				<div class="row">

					<div class="column small-6" @if(isset($value['Bold'])) style="font-weight: bolder;" @endif>{{$key}}</div>
					<div class="column small-2">{{$value['Clicks']}}</div>
					<div class="column small-2">{{$value['Conversions']}}</div>
					<div class="column small-2">@if($value['Dollar_Value'] != "--")${{number_format($value['Dollar_Value'], 2)}}@else{{$value['Dollar_Value']}}@endif</div>
				</div>
			@endforeach
		@endif

		<div class="row">
			<div class="column small-11 end"  style="float: left;border-bottom: 2px solid black;padding-bottom: 5px;color: #2AC56C;font-weight: bold;    padding-top: 0.5em;"> Advanced Paid</div>
		</div>

		@if(isset($today['advanced_paid']))
			@foreach($today['advanced_paid'] as $key => $value)
				<div class="row">

					<div class="column small-6" @if(isset($value['Bold'])) style="font-weight: bolder;" @endif>{{$key}}</div>
					<div class="column small-2">{{$value['Clicks']}}</div>
					<div class="column small-2">{{$value['Conversions']}}</div>
					<div class="column small-2">@if($value['Dollar_Value'] != "--")${{number_format($value['Dollar_Value'], 2)}}@else{{$value['Dollar_Value']}}@endif</div>
				</div>
			@endforeach
		@endif

		<div class="row">
			<div class="column small-11 end"  style="float: left;border-bottom: 2px solid black;padding-bottom: 5px;color: #2AC56C;font-weight: bold;    padding-top: 0.5em;"> Cost Per Enrollment</div>
		</div>

		@if(isset($today['cpe']))
			@foreach($today['cpe'] as $key => $value)
				<div class="row">

					<div class="column small-6" @if(isset($value['Bold'])) style="font-weight: bolder;" @endif>{{$key}}</div>
					<div class="column small-2">{{$value['Clicks']}}</div>
					<div class="column small-2">{{$value['Conversions']}}</div>
					<div class="column small-2">@if($value['Dollar_Value'] != "--")${{number_format($value['Dollar_Value'], 2)}}@else{{$value['Dollar_Value']}}@endif</div>
				</div>
			@endforeach
		@endif

		<div class="row">
			<div class="column small-11 end"  style="float: left;border-bottom: 2px solid black;padding-bottom: 5px;color: #2AC56C;font-weight: bold;    padding-top: 0.5em;"> Number of Users</div>
		</div>

		@if(isset($today['num_users']))
			@foreach($today['num_users'] as $key => $value)
				<div class="row">

					<div class="column small-6" @if(isset($value['Bold'])) style="font-weight: bolder;" @endif>{{$key}}</div>
					<div class="column small-2">{{$value['Clicks']}}</div>
					<div class="column small-2">{{$value['Conversions']}}</div>
					<div class="column small-2">@if($value['Dollar_Value'] != "--")${{number_format($value['Dollar_Value'], 2)}}@else{{$value['Dollar_Value']}}@endif</div>
				</div>
			@endforeach
		@endif

		<div class="row">
			<div class="column small-11 end"  style="float: left;border-bottom: 2px solid black;padding-bottom: 5px;color: #2AC56C;font-weight: bold;    padding-top: 0.5em;"> Number of Profiles</div>
		</div>

		@if(isset($today['num_profile']))
			@foreach($today['num_profile'] as $key => $value)
				<div class="row">

					<div class="column small-6" @if(isset($value['Bold'])) style="font-weight: bolder;" @endif>{{$key}}</div>
					<div class="column small-2">{{$value['Clicks']}}</div>
					<div class="column small-2">{{$value['Conversions']}}</div>
					<div class="column small-2">@if($value['Dollar_Value'] != "--")${{number_format($value['Dollar_Value'], 2)}}@else{{$value['Dollar_Value']}}@endif</div>
				</div>
			@endforeach
		@endif
	</div>
	<div class="column large-4 small-12" style="padding-top: 1em;">
		<div class="row">
			<div class="column small-12 text-center" style="color: Tomato;">Yesterday's Stats</div>
		</div>
		<div class="row">
			<div class="column small-4">Source</div>
			<div class="column small-3 text-right">Clicks</div>
			<div class="column small-3 text-right">Conversions</div>
			<div class="column small-2 text-left">Value</div>
		</div>

		@if(isset($yesterday['top']))
			@foreach($yesterday['top'] as $key => $value)
				<div class="row">

					<div class="column small-6" @if(isset($value['Bold'])) style="font-weight: bolder;" @endif>{{$key}}</div>
					<div class="column small-2">{{$value['Clicks']}}</div>
					<div class="column small-2">{{$value['Conversions']}}</div>
					<div class="column small-2">@if($value['Dollar_Value'] != "--")${{number_format($value['Dollar_Value'], 2)}}@else{{$value['Dollar_Value']}}@endif</div>
				</div>
			@endforeach
		@endif

		<div class="row">
			<div class="column small-11 end"  style="float: left;border-bottom: 2px solid black;padding-bottom: 5px;color: #2AC56C;font-weight: bold;    padding-top: 0.5em;"> Advanced Paid</div>
		</div>

		@if(isset($yesterday['advanced_paid']))
			@foreach($yesterday['advanced_paid'] as $key => $value)
				<div class="row">

					<div class="column small-6" @if(isset($value['Bold'])) style="font-weight: bolder;" @endif>{{$key}}</div>
					<div class="column small-2">{{$value['Clicks']}}</div>
					<div class="column small-2">{{$value['Conversions']}}</div>
					<div class="column small-2">@if($value['Dollar_Value'] != "--")${{number_format($value['Dollar_Value'], 2)}}@else{{$value['Dollar_Value']}}@endif</div>
				</div>
			@endforeach
		@endif

		<div class="row">
			<div class="column small-11 end"  style="float: left;border-bottom: 2px solid black;padding-bottom: 5px;color: #2AC56C;font-weight: bold;    padding-top: 0.5em;"> Cost Per Enrollment</div>
		</div>

		@if(isset($yesterday['cpe']))
			@foreach($yesterday['cpe'] as $key => $value)
				<div class="row">

					<div class="column small-6" @if(isset($value['Bold'])) style="font-weight: bolder;" @endif>{{$key}}</div>
					<div class="column small-2">{{$value['Clicks']}}</div>
					<div class="column small-2">{{$value['Conversions']}}</div>
					<div class="column small-2">@if($value['Dollar_Value'] != "--")${{number_format($value['Dollar_Value'], 2)}}@else{{$value['Dollar_Value']}}@endif</div>
				</div>
			@endforeach
		@endif

		<div class="row">
			<div class="column small-11 end"  style="float: left;border-bottom: 2px solid black;padding-bottom: 5px;color: #2AC56C;font-weight: bold;    padding-top: 0.5em;"> Number of Users</div>
		</div>

		@if(isset($yesterday['num_users']))
			@foreach($yesterday['num_users'] as $key => $value)
				<div class="row">

					<div class="column small-6" @if(isset($value['Bold'])) style="font-weight: bolder;" @endif>{{$key}}</div>
					<div class="column small-2">{{$value['Clicks']}}</div>
					<div class="column small-2">{{$value['Conversions']}}</div>
					<div class="column small-2">@if($value['Dollar_Value'] != "--")${{number_format($value['Dollar_Value'], 2)}}@else{{$value['Dollar_Value']}}@endif</div>
				</div>
			@endforeach
		@endif

		<div class="row">
			<div class="column small-11 end"  style="float: left;border-bottom: 2px solid black;padding-bottom: 5px;color: #2AC56C;font-weight: bold;    padding-top: 0.5em;"> Number of Profiles</div>
		</div>

		@if(isset($yesterday['num_profile']))
			@foreach($yesterday['num_profile'] as $key => $value)
				<div class="row">

					<div class="column small-6" @if(isset($value['Bold'])) style="font-weight: bolder;" @endif>{{$key}}</div>
					<div class="column small-2">{{$value['Clicks']}}</div>
					<div class="column small-2">{{$value['Conversions']}}</div>
					<div class="column small-2">@if($value['Dollar_Value'] != "--")${{number_format($value['Dollar_Value'], 2)}}@else{{$value['Dollar_Value']}}@endif</div>
				</div>
			@endforeach
		@endif
	</div>
	<div class="column large-4 small-12" style="padding-top: 1em;">
		<div class="row">
			<div class="column small-12 text-center" style="color: Tomato;">This Month's Stats</div>
		</div>
		<div class="row">
			<div class="column small-4">Source</div>
			<div class="column small-3 text-right">Clicks</div>
			<div class="column small-3 text-right">Conversions</div>
			<div class="column small-2 text-left">Value</div>
		</div>

		@if(isset($this_month['top']))
			@foreach($this_month['top'] as $key => $value)
				<div class="row">

					<div class="column small-6" @if(isset($value['Bold'])) style="font-weight: bolder;" @endif>{{$key}}</div>
					<div class="column small-2">{{$value['Clicks']}}</div>
					<div class="column small-2">{{$value['Conversions']}}</div>
					<div class="column small-2">@if($value['Dollar_Value'] != "--")${{number_format($value['Dollar_Value'], 2)}}@else{{$value['Dollar_Value']}}@endif</div>
				</div>
			@endforeach
		@endif

		<div class="row">
			<div class="column small-11 end"  style="float: left;border-bottom: 2px solid black;padding-bottom: 5px;color: #2AC56C;font-weight: bold;    padding-top: 0.5em;"> Advanced Paid</div>
		</div>

		@if(isset($this_month['advanced_paid']))
			@foreach($this_month['advanced_paid'] as $key => $value)
				<div class="row">

					<div class="column small-6" @if(isset($value['Bold'])) style="font-weight: bolder;" @endif>{{$key}}</div>
					<div class="column small-2">{{$value['Clicks']}}</div>
					<div class="column small-2">{{$value['Conversions']}}</div>
					<div class="column small-2">@if($value['Dollar_Value'] != "--")${{number_format($value['Dollar_Value'], 2)}}@else{{$value['Dollar_Value']}}@endif</div>
				</div>
			@endforeach
		@endif

		<div class="row">
			<div class="column small-11 end"  style="float: left;border-bottom: 2px solid black;padding-bottom: 5px;color: #2AC56C;font-weight: bold;    padding-top: 0.5em;"> Cost Per Enrollment</div>
		</div>

		@if(isset($this_month['cpe']))
			@foreach($this_month['cpe'] as $key => $value)
				<div class="row">

					<div class="column small-6" @if(isset($value['Bold'])) style="font-weight: bolder;" @endif>{{$key}}</div>
					<div class="column small-2">{{$value['Clicks']}}</div>
					<div class="column small-2">{{$value['Conversions']}}</div>
					<div class="column small-2">@if($value['Dollar_Value'] != "--")${{number_format($value['Dollar_Value'], 2)}}@else{{$value['Dollar_Value']}}@endif</div>
				</div>
			@endforeach
		@endif

		<div class="row">
			<div class="column small-11 end"  style="float: left;border-bottom: 2px solid black;padding-bottom: 5px;color: #2AC56C;font-weight: bold;    padding-top: 0.5em;"> Number of Users</div>
		</div>

		@if(isset($this_month['num_users']))
			@foreach($this_month['num_users'] as $key => $value)
				<div class="row">

					<div class="column small-6" @if(isset($value['Bold'])) style="font-weight: bolder;" @endif>{{$key}}</div>
					<div class="column small-2">{{$value['Clicks']}}</div>
					<div class="column small-2">{{$value['Conversions']}}</div>
					<div class="column small-2">@if($value['Dollar_Value'] != "--")${{number_format($value['Dollar_Value'], 2)}}@else{{$value['Dollar_Value']}}@endif</div>
				</div>
			@endforeach
		@endif

		<div class="row">
			<div class="column small-11 end"  style="float: left;border-bottom: 2px solid black;padding-bottom: 5px;color: #2AC56C;font-weight: bold;    padding-top: 0.5em;"> Number of Profiles</div>
		</div>

		@if(isset($this_month['num_profile']))
			@foreach($this_month['num_profile'] as $key => $value)
				<div class="row">

					<div class="column small-6" @if(isset($value['Bold'])) style="font-weight: bolder;" @endif>{{$key}}</div>
					<div class="column small-2">{{$value['Clicks']}}</div>
					<div class="column small-2">{{$value['Conversions']}}</div>
					<div class="column small-2">@if($value['Dollar_Value'] != "--")${{number_format($value['Dollar_Value'], 2)}}@else{{$value['Dollar_Value']}}@endif</div>
				</div>
			@endforeach
		@endif
	</div>
</div>

<div class="row"  style="max-width: 95%;margin-top: 50px;">
	<div class="column small-12">
		********************************************************
	</div>
</div>
<div class="row"  style="max-width: 95%;margin-top: 50px;">
	<div class="column small-4">
		Today appended:
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
<div class="row"  style="max-width: 95%;">
	<div class="column small-4">
		Yesterday appended:
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
<div class="row"  style="max-width: 95%;margin-bottom: : 10px;">
	<div class="column small-4">
		This month appended:
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

<div class="row"  style="max-width: 95%;margin-top: 50px;">
	<div class="column small-12">
		********************************************************
	</div>
</div>
<div class="row"  style="max-width: 95%;margin-top: 50px;">
	<div class="column small-4">
		Today clicks:
	</div>
	<div class="column small-2 end">
		{{$today_clicks}}
	</div>
</div>
<div class="row"  style="max-width: 95%;">
	<div class="column small-4">
		Yesterday clicks:
	</div>
	<div class="column small-2 end">
		{{$yesterday_clicks}}
	</div>
</div>
<div class="row"  style="max-width: 95%;margin-bottom: : 10px;">
	<div class="column small-4">
		This month clicks:
	</div>
	<div class="column small-2 end">
		{{$this_month_clicks}}
	</div>
</div>
@stop