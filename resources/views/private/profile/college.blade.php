@extends('private.ranking.master')

@section('sidebar')    
    @include('public.includes.publicrightpanel')
@stop

@section('content')
    @include('public.includes.publicheader')
<div class="row">
	<div class="small-12 columns school_attended_box">
    	<div class="row">
        	<div class="small-6 columns white-18-bold">Schools Attended</div>
            <div class="small-3 columns white-16-bold">Total Courses</div>
            <div class="small-3 columns white-16-bold">Total Units</div>
        </div>
        <div class="row">
        	<div class="small-6 columns white-16-bold" style="border-left:solid 3px #26b24b;">San Jose State University*</div>
            <div class="small-3 columns white-18-bold">13</div>
            <div class="small-3 columns white-18-bold">13</div>
        </div>
        <div class="row">
        	<div class="small-6 columns white-16-bold" style="border-left:solid 3px #26C9FF;">Saint Mary’s College of California</div>
            <div class="small-3 columns white-18-bold">2</div>
            <div class="small-3 columns white-18-bold">2</div>
        </div>
    </div>
</div>
@stop
