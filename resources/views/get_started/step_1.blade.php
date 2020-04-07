@extends('get_started.master')

@section('content')
	
	<div id="get_started_step2" class="gs_step" data-step="{{$currentStep}}"></div>
	<script src="/js/prod_ready/getstarted/BreadCrumb_Step2_Components.min.js?v=1.08" defer></script>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script>
		$(document).ready(function(){
			setTimeout(() => {
				var leadid_token = $("input[name='universal_leadid']").attr('value');
				console.log( leadid_token );
				var data = { leadid: leadid_token };
				$.ajax({
					type: 'POST',
					url: "/get_started/save?step=leadid",
					data: data,
					headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				})
				.done(function() {
					console.log( "success" );
				})
				.fail(function() {
					console.log( "error" );
				});
			}, 500);
		});
	</script>
	
@stop