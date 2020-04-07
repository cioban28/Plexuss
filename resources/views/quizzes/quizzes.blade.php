<div class='row collapse'>
	<div class='small-12 column'>
		<div id="quizCarousel" class="owl-carousel owl-theme">
			@foreach ($quizInfo as $quizInfo)
			
				<div class="quiz-box darktheme item" data-quizid="{{ $quizInfo->id }}">
					<div class="row quiz-header-area text-left">
						<div class="column small-11 small-centered header">
							COLLEGE TRIVIA:
						</div>
						<div class="column small-11 small-centered question">
							{{ $quizInfo->question }}
						</div>
						<div class="column small-11 small-centered pass">
							<span>Correct!</span>{{ $quizInfo->percent }} of users choose this
						</div>

						<div class="column small-11 small-centered fail">
							<span>Oops :/</span>{{ $quizInfo->percentfail }} of users choose this
						</div>
					</div>

					<div class="row quiz-image-area collapse text-center">
						<div class="column small-12">
							<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/quiz/{{ $quizInfo->image }}" alt="" width="100%">
							<div class="imagename">{{ $quizInfo->image_title }}</div>
						</div>
					</div>

					<div class="row quiz-question-area text-left">
						<!-- these need to be random!! -->

						@foreach ($quizInfo->answers as $answers)
							<div class="column small-10 small-centered">
								<div class="row collapse">
									<div class="column small-2">{{ Form::radio('selection', $answers->value, null, array('class' => 'inline' ) ) }}</div>
									<div class="column small-10"><label for="selection" class="">{{ $answers->option }}</label></div>
								</div>
							</div>
						@endforeach

					</div>

					<div class="row">
						<div class="column small-12 quiz-answer-area text-left">
							{{ $quizInfo->blurb }}
						</div>
					</div>
					
					<div class="row error">
						<div class="column small-10 small-centered">
							Please Select an item above to submit.
						</div>
					</div>

					<div class="row submitbutton">
						<div class="column quiz-submit-area small-12 text-center">
							<div class="button" onclick="submitQuiz(this)">Submit answers</div>
						</div>
					</div>
					<div class="row linkbutton">
						<div class="column quiz-submit-area small-12">
							<a href="{{ $quizInfo->page_link }}">
								<div class="button">Check out this school!</div>
							</a>
						</div>
					</div>
				</div>

			@endforeach
		</div>	
		<div class="row" id="quizControls">
			@if ( isset($quizInfo) )
			<div class="column small-6 text-left left"><span class="quizArrow prev"></span>Prev</div>
			<div class="column small-6 text-right right">Next<span class="quizArrow next"></span></div>
			@endif
		</div>
		<script>
			function submitQuiz(elem){
				var quiz = $(elem).parents('.quiz-box');
				var checkedItem = quiz.find("input[name='selection']:checked").val();
				var quizId = quiz.data('quizid');
				var result = '';

				if (!checkedItem) {
				 	quiz.find('.error').show();
				} else {
					
					//if pass show pass items, if fail show fail
					if (checkedItem == 'true') {
						quiz.find('.pass').show();
						result = 1;
					} else {
						quiz.find('.fail').show();
						result = 0;
					};

					//show the common parts to a responce of a answer.
					quiz.find('.error').hide();
					quiz.find('.quiz-question-area').hide();
					quiz.find('.submitbutton').hide();
					quiz.find('.linkbutton').show();
					quiz.find('.quiz-answer-area').show();

					//make a ajax post to the server and record the results!
					$.ajax({
						url: '/ajax/quiz',
						type: 'POST',
						data: {
							quiz_id: quizId,
							quizresult: result
						},
						headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
					});
				}
			}
		</script>
	</div>
</div>