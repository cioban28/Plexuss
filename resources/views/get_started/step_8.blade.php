@extends('get_started.master')

@section('content')
	
	<div id="get_started_step8" class="gs_step" data-step="{{$currentStep}}"></div>	

	<div id="coveted_schedular" class="row collapse stylish-scrollbar-mini">
		<div class="column small-12">
			<iframe src="https://plexuss-premium.youcanbook.me/?noframe=true&skipHeaderFooter=true" id="ycbmiframeplexuss-premium" style="width:100%;height:1000px;border:0px;background-color:transparent;" frameborder="0" allowtransparency="true"></iframe><script>window.addEventListener && window.addEventListener("message", function(event){if (event.origin === "https://plexuss-premium.youcanbook.me"){document.getElementById("ycbmiframeplexuss-premium").style.height = event.data + "px";}}, false);</script>
		</div>
	</div>

	<script src="/js/prod_ready/getstarted/BreadCrumb_Step8_Components.min.js?v=1.05" defer></script>
	
@stop