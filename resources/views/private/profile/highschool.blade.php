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
        	<div class="small-6 columns white-16-bold" style="border-left:solid 3px #26b24b;">Kennedy Highschool*</div>
            <div class="small-3 columns white-18-bold">13</div>
            <div class="small-3 columns white-18-bold">13</div>
        </div>
        <div class="row">
        	<div class="small-6 columns white-16-bold" style="border-left:solid 3px #26C9FF;">New Milford Highschool</div>
            <div class="small-3 columns white-18-bold">2</div>
            <div class="small-3 columns white-18-bold">2</div>
        </div>
    </div>
</div>
<div class="row">
	<div class="small-12 columns profile_view_content_box">&nbsp;</div>
</div>
@stop