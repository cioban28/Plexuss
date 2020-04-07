<!doctype html>
<html class="no-js" lang="en">

	<head>
		@include('private.headers.header')
	</head>
	<body id="{{$currentPage}}">
		@include('private.includes.topnav')
		<div class="row collapse">

            <div class="off-canvas-wrap" data-offcanvas>
            	<div class="inner-wrap">
					<div class='medium-12 column no-padding'>            
						@yield('content')
					</div>
           		 	<a class="exit-off-canvas"></a>                
               </div>
            </div>  

		</div>
		@include('private.footers.footer')	

		<!-- Webinar onclick event -->
		<script type="text/javascript">

			function webinarSubmit(){

			  $.ajax({
					url:'/webinar/submit',
					type: 'get',
					headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
					success:function(data){
					    if(data == 'Completed'){
					    	$('#notRegistered').hide();
					    	$('#registered').show();
					    }
					}
				});
			}

		</script>	
	</body>
</html>
