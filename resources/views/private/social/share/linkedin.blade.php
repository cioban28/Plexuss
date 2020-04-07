<!doctype html>
<html>
	<head>
		<link rel="stylesheet" href="/css/foundation.min.css?6" />
		<link rel="stylesheet" href="/css/default.css?6" />
		<link rel="stylesheet" href="/css/profile.css?6" />
		<script src="/js/vendor/modernizr.js?7"></script>
		<style>
			.main-box{
				background-color: #f5f5f5;
				border-top-left-radius: 0.5em;
				border-bottom-left-radius: 0.5em;
				padding-top: 0.4em;
				margin-top: 1em;
				margin-bottom: 1em;
			}

			.title-span{
				color: #004358;
				font-weight: bold;
			}

			.title-sub{
				color: #b9babb;
				font-size: 0.75em;
			}

			.preview-wrapper{
				margin-bottom: 1em;
			}

			.preview-text-wrapper{
				line-height: 1em;
			}

			.preview-image-wrapper img{
				border: 1px solid #b9babb;
			}

			.wrapper{
				padding-left: 1em;
			}

		</style>
	</head>
	<body>
		<div class='row'>
			<div class='small-12 column wrapper'>
				<!--//////////////////// Main gray box \\\\\\\\\\\\\\\\\\\\-->
				<div class='row'>
					<div class='small-12 column main-box'>
						<div class='row'>
							<div class='small-12 column'>
								{{ Form::open( array( 'url' => '/social/share/linkedin/submit', 'method' => 'POST', 'data-abide' ) ) }}
								<!--
									-picture
									-title
									-href
									-comment
									-visibility
								-->
								<div class='row'>
									<div class='small-12 column preview-wrapper'>
										<div class='row'>
											<div class='small-3 medium-2 column preview-image-wrapper'>
												<img src='{{ $picture }}' alt='Share Image' class='share-image' />
											</div>
											<div class='small-9 medium-10 column preview-text-wrapper'>
												<div class='row'>
													<div class='small-12 column'>
														<span class='title-span'>
															{{ htmlentities( $title ) }}
														</span>
													</div>
												</div>
												<div class='row'>
													<div class='small-12 column'>
														<span class='title-sub'>
															PLEXUSS.COM
														</span>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class='row'>
									<div class='small-12 column comment-wrapper'>
										{{ Form::textarea( 'comment', '', array( 'id' => 'comment', 'rows' => '2', 'placeholder' => 'Enter your comment', 'required' )  ) }}
										<small class='error'>Please enter a comment</small>
									</div>
								</div>
								<div class='row'>
									<div class='small-4 column visibility-wrapper'>
										{{ Form::select( 'visibility', array( 'anyone' => 'Anyone', 'connections-only' => 'Connections Only' ), 'connections-only', array( 'id' => 'visibility' ) ) }}
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!--\\\\\\\\\\\\\\\\\\\\ Main gray box ////////////////////-->
				<div class='row'>
					<div class='small-12 column button-wrapper'>
						<div class='row'>
							<div class='small-6 column'>
								<div class='button btn-cancel' onclick='window.close()'>
									Cancel
								</div>
							</div>
							<div class='small-6 column'>
								{{ Form::submit( 'Share on LinkedIn', array( 'class' => 'button btn-Save' ) ) }}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- End of container -->
	</body>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="/js/foundation/foundation.js?7"></script>
	<script src="/js/foundation/foundation.abide.js?7"></script>
	<script type='text/javascript'>
		$(document).ready( function(){
			$(document).foundation();
		} );
	</script>
</html>
