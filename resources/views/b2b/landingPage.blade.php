@extends('b2b.master')

@section('b2b-content')
  <div class="home_container">
  	<div class="row">
	  	<div class="first_heading">
	  		<span class="total_students" data-students="5500000">0,000,000</span>
	  		<span class="student_label">Million Students</span>
	  		<div class="tag_line">One Global Platform of Opportunity</div>
	  	</div>
	  	<div class="second_heading">
	  		<span class="bold">PLEXUSS</span> helps enable nearly 700 colleges and universities to better Recruit, Retain, and drive positive Results.
	  	</div>
	  	<a href="/solutions/contact-us"><div class="btn-container"><button class="engage_more">ENGAGE MORE</button></div></a>
  	</div>
  </div>

  @include('b2b.b2bFooter')
@stop
