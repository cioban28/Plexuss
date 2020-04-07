<!doctype html>
<html class="no-js" lang="en">
	<head>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		@include('private.headers.header')
	</head>
	<body id="{{$currentPage}}">
		@if( isset($is_agency) && $is_agency == 1 )
			@include('private.includes.agencyTopNav')
		@else
			@include('private.includes.topnav')
		@endif
	
		@yield('content')

		<!-- delete filter modal -->
	<div id="delete-filter-template-modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
		<div class="clearfix">
			<div class="right">
				<a class="close-reveal-modal help_helpful_videos_close_icon" aria-label="Close">&#215;</a>
			</div>
		</div>
		<div class="row">
			<div class="column small-12 top-message text-center">
				Are you sure you want to delete filter
			</div>
			<div class="column small-12 end text-center filterName">
			</div>
		</div>
		<div class="row btnrow">
			<div class="column small-offset-2 cancel-filter-template-btn small-4 close-reveal-modal text-center">Cancel</div>
			<div class="column small-4 end delete-filter-template-btn text-center" data-fval="" data-ftxt="" onClick="Plex.studentSearch.deleteFilter();">Delete</div>
		</div>
	</div>



	<!-- File attachment modal - file attachments are organized by user -->
	@include('includes.fileAttachmentModal')
	
	<!-- template modals -->
	@include('private.includes.editMessageTemplate')

		@include('private.footers.footer')
	</body>
</html>
