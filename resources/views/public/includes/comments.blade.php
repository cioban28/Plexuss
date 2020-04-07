<div class='row'>
	<div class='small-12 column comment-wrapper'>
		<a name='comments'></a>
		<!--//////////////////// COMMENT FORM WRAPPER \\\\\\\\\\\\\\\\\\\\-->
		<div class='row'>
			<div class='small-12 column comment-form-wrapper'>
				{{ Form::open( array( 'url' => '/social/comment/new', 'data-abide' => 'ajax', 'method' => 'POST', 'id' => 'comment_form' ) ) }}
					{{ Form::hidden( 'comment_thread', isset( $comments['thread_id'] ) ? $comments['thread_id'] : null, array( 'id' => 'comment_thread' ) ) }}
					{{ Form::hidden( 'latest_comment_id', isset( $comments['latest_comment_id'] ) ? $comments['latest_comment_id'] : 0, array( 'id' => 'latest_comment_id', 'autocomplete' => 'off' ) ) }}
					{{ Form::hidden( 'earliest_comment_id', isset( $comments['earliest_comment_id'] ) ? $comments['earliest_comment_id'] : null, array( 'id' => 'earliest_comment_id', 'autocomplete' => 'off' ) ) }}
					{{ Form::hidden( 'item_id', isset( $item_id ) ? $item_id : null ) }}
					{{ Form::hidden( 'parent', '' ) }}
					<div class='row'>
						<div class='small-12 column comment-big-icon-wrapper'>
							<img id='comments-title-icon' src="/images/social/comments/comments-icon.png" alt=""/>
							<span id='comments-title'>Comments</span>
						</div>
					</div>
					<div class='row'>
						<div class='small-12 column comment-psuedo-box text-center'>
							<span id='comment-psuedo-box-ldquo-big'> &ldquo; </span>
							<span id='comment-psuedo-box-rdquo-big'> &rdquo; </span>
							<span id='comment-psuedo-box-ldquo'> &ldquo; </span>
							<span id='comment-psuedo-box-rdquo'> &rdquo; </span>
							<span id='comment-psuedo-box-char-count'></span>
							<span id='comment-psuedo-box-placeholder'>Post a comment</span>
							<div class='row'>
								<div class='small-12 column text-left'>
									@if( !$signed_in )
										<div id='comment_textarea_guest'>
											<a href='/signin'>
												Sign in to comment
											</a>
										</div>
									@else
										{{ Form::textarea( 'comment_textarea', '', array( 'id' => 'comment_textarea', 'rows' => '6', 'required', 'pattern' => 'onechar' )) }}
									@endif
									<small class='error'>Please enter a comment of at least one character</small>
								</div>
							</div>
						</div>
						@if( $signed_in )
						<div class='row collapse'>
							<div class='small-12 column text-right'>
								{{ Form::checkbox( 'post_anon', 1, isset( $comments['anon'] ) && $comments['anon'] == 1 ? true : false, array( 'id' => 'post_anon', 'autocomplete' => 'off' ) ) }}
								{{ Form::label( 'post_anon', "Don't show my name", array( 'class' => 'comment-label' ) ) }}
								{{ Form::Submit( 'Post', array( 'class' => 'button btn-save', 'id' => 'comment-post-btn' ) ) }}
							</div>
						</div>
						@endif
					</div>
				{{ Form::close() }}
			</div>
		</div>
		<!--\\\\\\\\\\\\\\\\\\\\ COMMENT FORM WRAPPER ////////////////////-->
		<!--//////////////////// COMMENT THREAD WRAPPER \\\\\\\\\\\\\\\\\\\\-->
		<div class='row'>
			<div class='small-12 column comment-thread-wrapper' {{ isset( $comments['comments'] ) ? 'data-comments="' . htmlentities( $comments['comments'] ) . '"' : null }}>
				<!--//////////////////// Individual Comment \\\\\\\\\\\\\\\\\\\\ -->
				<!--
				<div class='row'>
					<div class='small-12 column comment-item' data-offset='0' data-comment_id='1'>
						<div class='row'>
							<div class='small-12 column'>
								<div class='comment-user-image'>
								</div>
								<div class='comment-user-info'>
									<div>
										<span class='comment-user-name'>
											Lettuce
										</span>
									</div>
									<div>
										<span class='comment-user-type'>
											Student
										</span>
									</div>
								</div>
							</div>
							<div class='medium-10 column'>
								<div class='row'>
									<div class='small-12 column'>
									</div>
								</div>
								<div class='row'>
									<div class='small-12 column'>
									</div>
								</div>
							</div>
						</div>
						<div class='row'>
							<div class='small-12 column'>
								<span class='comment-content'>
									Herp derpsum herpsum der sherpus, dee herpler derperker. Herp merp tee herpderpsmer sherp derps. Derp derpsum berp herpler dee herpy derperker merp serp derps.
								</span>
							</div>
						</div>
						<div class='row'>
							<div class='small-12 column text-right comment-interaction'>
								<span> I like this!</span>
								<span> Reply </span>
							</div>
						</div>
					</div>
				</div>
				-->
				<!--\\\\\\\\\\\\\\\\\\\\ Individual Comment //////////////////// -->
			</div>
		</div>
		<!--\\\\\\\\\\\\\\\\\\\\ COMMENT THREAD WRAPPER ////////////////////-->
	</div>
</div>
