@extends('betausers.master')
@section('content')
<div class="row" style="max-width: 90%;">
	<div class="column large-4 small-12" style="padding-top: 1em;">
		<div class="row">
			<div class="column small-12 text-center" style="color: Tomato;">Today's Stat</div>
		</div>
		<div class="row">
			<div class="column small-3">Source</div>
			<div class="column small-3">Clicks</div>
			<div class="column small-3">Conversions</div>
			<div class="column small-3">Value</div>
		</div>
		@foreach($today as $key)
			@if (strpos($key->Source, 'nrccua') !== FALSE)
				<div class="row">

					<div class="column small-3">{{$key->Source}}</div>
					<div class="column small-3">{{$key->Clicks}}</div>
					<div class="column small-3">{{$key->Conversions}}</div>
					<div class="column small-3">@if($key->Dollar_Value != "--")${{number_format($key->Dollar_Value, 2)}}@else{{$key->Dollar_Value}}@endif</div>
				</div>
			@endif
		@endforeach
	</div>
	<div class="column large-4 small-12" style="padding-top: 1em;">
		<div class="row">
			<div class="column small-12 text-center" style="color: Tomato;">Yesterday's Stat</div>
		</div>
		<div class="row">
			<div class="column small-3">Source</div>
			<div class="column small-3">Clicks</div>
			<div class="column small-3">Conversions</div>
			<div class="column small-3">Value</div>
		</div>
		@foreach($yesterday as $key)
			@if (strpos($key->Source, 'nrccua') !== FALSE)
				<div class="row">

					<div class="column small-3">{{$key->Source}}</div>
					<div class="column small-3">{{$key->Clicks}}</div>
					<div class="column small-3">{{$key->Conversions}}</div>
					<div class="column small-3">@if($key->Dollar_Value != "--")${{number_format($key->Dollar_Value, 2)}}@else{{$key->Dollar_Value}}@endif</div>
				</div>
			@endif
		@endforeach
	</div>
	<div class="column large-4 small-12" style="padding-top: 1em;">
		<div class="row">
			<div class="column small-12 text-center" style="color: Tomato;">This month's Stat</div>
		</div>
		<div class="row">
			<div class="column small-3">Source</div>
			<div class="column small-3">Clicks</div>
			<div class="column small-3">Conversions</div>
			<div class="column small-3">Value</div>
		</div>
		@foreach($this_month as $key)
			@if (strpos($key->Source, 'nrccua') !== FALSE)
				<div class="row">

					<div class="column small-3">{{$key->Source}}</div>
					<div class="column small-3">{{$key->Clicks}}</div>
					<div class="column small-3">{{$key->Conversions}}</div>
					<div class="column small-3">@if($key->Dollar_Value != "--")${{number_format($key->Dollar_Value, 2)}}@else{{$key->Dollar_Value}}@endif</div>
				</div>
			@endif
		@endforeach
	</div>
</div>

@stop