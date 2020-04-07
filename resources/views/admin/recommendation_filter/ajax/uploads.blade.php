<div class="row filter-by-uploads-container filter-page-section" data-section="uploads">
	<div class="column small-12">
	
		{{Form::open()}}

			@foreach( $filters as $uploads )
			<div class="row">
				<div class="column small-12 filter-instructions">
					In your recommended students, we will give priority to students that have uploaded their:			
				</div>
			</div>

			<!-- filter by transcripts -->
			<div class="row">
				<div class="column small-12">
					@if( !empty($uploads) )
					{{Form::checkbox('uploads', 'transcript', isset($uploads['uploads']['transcript_filter']), array('id'=>'transcript_filter', 'class' => 'checkbox-filter filter-this'))}}
					@else
					{{Form::checkbox('uploads', 'transcript', true, array('id'=>'transcript_filter', 'class' => 'checkbox-filter filter-this'))}}
					@endif
					{{Form::label('transcript_filter', 'Transcript')}}
				</div>
			</div>

			<!-- filter by financial info -->
			<div class="row">
				<div class="column small-12">
					@if( !empty($uploads) )
					{{Form::checkbox('uploads', 'financialInfo', isset($uploads['uploads']['financialInfo_filter']), array('id'=>'financialInfo_filter', 'class' => 'checkbox-filter filter-this'))}}
					@else
					{{Form::checkbox('uploads', 'financialInfo', true, array('id'=>'financialInfo_filter', 'class' => 'checkbox-filter filter-this'))}}
					@endif
					{{Form::label('financialInfo_filter', 'Financial Info (Int\'l Only)')}}
				</div>
			</div>

			<!-- filter by copy of ielts score -->
			<div class="row">
				<div class="column small-12">
					@if( !empty($uploads) )
					{{Form::checkbox('uploads', 'ielts', isset($uploads['uploads']['ielts_fitler']), array('id'=>'ielts_fitler', 'class' => 'checkbox-filter filter-this'))}}
					@else
					{{Form::checkbox('uploads', 'ielts', true, array('id'=>'ielts_fitler', 'class' => 'checkbox-filter filter-this'))}}
					@endif
					{{Form::label('ielts_fitler', 'Copy of IELTS score')}}
				</div>
			</div>

			<!-- filter by copy of toefl score -->
			<div class="row">
				<div class="column small-12">
					@if( !empty($uploads) )
					{{Form::checkbox('uploads', 'toefl', isset($uploads['uploads']['toefl_filter']), array('id'=>'toefl_filter', 'class' => 'checkbox-filter filter-this'))}}
					@else
					{{Form::checkbox('uploads', 'toefl', true, array('id'=>'toefl_filter', 'class' => 'checkbox-filter filter-this'))}}
					@endif
					{{Form::label('toefl_filter', 'Copy of TOEFL score')}}
				</div>
			</div>

			<!-- filter by resume/cv -->
			<div class="row">
				<div class="column small-12">
					@if( !empty($uploads) )
					{{Form::checkbox('uploads', 'resume', isset($uploads['uploads']['resume_filter']), array('id'=>'resume_filter', 'class' => 'checkbox-filter filter-this'))}}
					@else
					{{Form::checkbox('uploads', 'resume', true, array('id'=>'resume_filter', 'class' => 'checkbox-filter filter-this'))}}
					@endif
					{{Form::label('resume_filter', 'Resume / CV')}}
				</div>
			</div>

			<!-- filter by passport -->
			<div class="row">
				<div class="column small-12">
					@if( !empty($uploads) )
					{{Form::checkbox('uploads', 'passport', isset($uploads['uploads']['passport_filter']), array('id'=>'passport_filter', 'class' => 'checkbox-filter filter-this'))}}
					@else
					{{Form::checkbox('uploads', 'passport', true, array('id'=>'passport_filter', 'class' => 'checkbox-filter filter-this'))}}
					@endif
					{{Form::label('passport_filter', 'Passport')}}
				</div>
			</div>

			<!-- filter by essay -->
			<div class="row">
				<div class="column small-12">
					@if( !empty($uploads) )
					{{Form::checkbox('uploads', 'essay', isset($uploads['uploads']['essay_filter']), array('id'=>'essay_filter', 'class' => 'checkbox-filter filter-this'))}}
					@else
					{{Form::checkbox('uploads', 'essay', true, array('id'=>'essay_filter', 'class' => 'checkbox-filter filter-this'))}}
					@endif
					{{Form::label('essay_filter', 'Essay')}}
				</div>
			</div>

			<!-- filter by other -->
			<div class="row">
				<div class="column small-12">
					@if( !empty($uploads) )
					{{Form::checkbox('uploads', 'other', isset($uploads['uploads']['other_filter']), array('id'=>'other_filter', 'class' => 'checkbox-filter filter-this'))}}
					@else
					{{Form::checkbox('uploads', 'other', true, array('id'=>'other_filter', 'class' => 'checkbox-filter filter-this'))}}
					@endif
					{{Form::label('other_filter', 'Other')}}
				</div>
			</div>
			@endforeach
			
		{{Form::close()}}

	</div>
</div>