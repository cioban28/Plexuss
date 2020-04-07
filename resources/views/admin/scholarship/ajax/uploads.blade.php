<div class="filter-crumbs-container">
  <ul class="inline-list filter-crumb-list">
    <li>
      <div class="clearfix">
        <div class="left section">{{$section}}: </div>
        	
			 @if(isset($filters))
			 	@foreach( $filters as $filter)
					@if(isset($filter["uploads"]))
						@php $uploads = $filter["uploads"]; @endphp
						
					@endif
				@endforeach
			@endif
	 	</div>
    </li>
  </ul>
</div>
<div class="row filter-by-uploads-container filter-page-section" data-section="uploads">
	<div class="column small-12">
			<div class="row">
				<div class="column small-12 filter-instructions">
					In your recommended students, we will give priority to students that have uploaded their:			
				</div>
			</div>

			<!-- filter by transcripts -->
			<div class="row">
				<div class="column small-12">
				
					@if( isset($uploads['transcript_filter']))
					{{Form::checkbox('uploads', 'transcript', $uploads['transcript_filter']== 'transcript_filter'? true : false, array('id'=>'transcript_filter', 'class' => 'checkbox-filter filter-this'))}}
					@else
					{{Form::checkbox('uploads', 'transcript', true, array('id'=>'transcript_filter', 'class' => 'checkbox-filter filter-this'))}}
					@endif
					{{Form::label('transcript_filter', 'Transcript')}}
				</div>
			</div>

			<!-- filter by financial info -->
			<div class="row">
				<div class="column small-12">
					@if( isset($uploads['financialInfo_filter']))
					{{Form::checkbox('uploads', 'financialInfo', $uploads['financialInfo_filter']== 'financialInfo_filter'? true : false, array('id'=>'financialInfo_filter', 'class' => 'checkbox-filter filter-this'))}}
					@else
					{{Form::checkbox('uploads', 'financialInfo', true, array('id'=>'financialInfo_filter', 'class' => 'checkbox-filter filter-this'))}}
					@endif
					{{Form::label('financialInfo_filter', 'Financial Info (Int\'l Only)')}}
				</div>
			</div>

			<!-- filter by copy of ielts score -->
			<div class="row">
				<div class="column small-12">
					@if( isset($uploads['ielts_fitler']))
					{{Form::checkbox('uploads', 'ielts', $uploads['ielts_fitler']== 'ielts_fitler'? true : false, array('id'=>'ielts_fitler', 'class' => 'checkbox-filter filter-this'))}}
					@else
					{{Form::checkbox('uploads', 'ielts', true, array('id'=>'ielts_fitler', 'class' => 'checkbox-filter filter-this'))}}
					@endif
					{{Form::label('ielts_fitler', 'Copy of IELTS score')}}
				</div>
			</div>

			<!-- filter by copy of toefl score -->
			<div class="row">
				<div class="column small-12">
					@if( isset($uploads['toefl_filter']))
					{{Form::checkbox('uploads', 'toefl',  $uploads['toefl_filter']== 'toefl_filter'? true : false, array('id'=>'toefl_filter', 'class' => 'checkbox-filter filter-this'))}}
					@else
					{{Form::checkbox('uploads', 'toefl', true, array('id'=>'toefl_filter', 'class' => 'checkbox-filter filter-this'))}}
					@endif
					{{Form::label('toefl_filter', 'Copy of TOEFL score')}}
				</div>
			</div>

			<!-- filter by resume/cv -->
			<div class="row">
				<div class="column small-12">
					@if( isset($uploads['resume_filter']))
					{{Form::checkbox('uploads', 'resume', $uploads['resume_filter']== 'resume_filter'? true : false, array('id'=>'resume_filter', 'class' => 'checkbox-filter filter-this'))}}
					@else
					{{Form::checkbox('uploads', 'resume', true, array('id'=>'resume_filter', 'class' => 'checkbox-filter filter-this'))}}
					@endif
					{{Form::label('resume_filter', 'Resume / CV')}}
				</div>
			</div>

			<!-- filter by passport -->
			<div class="row">
				<div class="column small-12">
					@if( isset($uploads['passport_filter']))
					{{Form::checkbox('uploads', 'passport', $uploads['passport_filter']== 'passport_filter'? true : false, array('id'=>'passport_filter', 'class' => 'checkbox-filter filter-this'))}}
					@else
					{{Form::checkbox('uploads', 'passport', false, array('id'=>'passport_filter', 'class' => 'checkbox-filter filter-this'))}}
					@endif
					{{Form::label('passport_filter', 'Passport')}}
				</div>
			</div>

			<!-- filter by essay -->
			<div class="row">
				<div class="column small-12">
					@if( isset($uploads['essay_filter']))
					{{Form::checkbox('uploads', 'essay', $uploads['essay_filter']== 'essay_filter'? true : false, array('id'=>'essay_filter', 'class' => 'checkbox-filter filter-this'))}}
					@else
					{{Form::checkbox('uploads', 'essay', false, array('id'=>'essay_filter', 'class' => 'checkbox-filter filter-this'))}}
					@endif
					{{Form::label('essay_filter', 'Essay')}}
				</div>
			</div>

			<!-- filter by other -->
			<div class="row">
				<div class="column small-12">
					@if( isset($uploads['other_filter']))
					{{Form::checkbox('uploads', 'other', $uploads['other_filter']== 'other_filter'? true : false, array('id'=>'other_filter', 'class' => 'checkbox-filter filter-this'))}}
					@else
					{{Form::checkbox('uploads', 'other', false, array('id'=>'other_filter', 'class' => 'checkbox-filter filter-this'))}}
					@endif
					{{Form::label('other_filter', 'Other')}}
				</div>
			</div>
			
		

	</div>
</div>