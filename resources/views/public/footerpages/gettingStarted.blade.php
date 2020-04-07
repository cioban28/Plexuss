@extends('public.footerpages.master')
@section('help_heading')
	{{$help_heading or ''}}
@stop
@section('content')
<div id="container-box" class="js-masonry row startpage" align="center">
	@include('public.includes.gettingStartedPins')
</div>
	<div class="clearfix"></div>
@stop
