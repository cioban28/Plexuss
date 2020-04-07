@extends('public.footerpages.master')
@section('content')
<div class='row'>
	<div class='text-center large-12 column'>
		<h1 class='header1'>Scholarship Submission Second Step</h1>
	</div>
</div>
<div class='row'>
	<div class='large-6 large-centered column'>
		@if($errors->any())
			<div class="alert alert-danger">
				{!! implode('', $errors->all('<li class="error">:message</li>')) !!}
			</div>
		@endif

	</div>
	<br/>
</div>
{{ Form::open(array('url' => '/scholarship-submission/'.$rndtoken , 'method' => 'POST')) }}
<div class='row'>
	<div class='large-centered large-6 end column scholar-sub-container'>
		<div class='row'>
			<div class='large-7 column'>
				{{ Form::label('financialNeedRequirement', 'Financial Need Requirement') }}
			</div>
            <div class='large-2 column'>
                <span data-tooltip aria-haspopup="true" class="has-tip" style="font-size: 15pt;" title="Students require financial statements to apply">&#9432;</span>
            </div>
			<div class='large-2 column'>
				{{ Form::checkbox('financialNeedRequirement', 'true' ) }}
			</div>
		</div>
		<div class="row">
			<div class="large-7 columns">
				{{ Form::label('minAct', 'Minimum ACT Score *') }}
			</div>
			<div class="large-2 columns">
				{{ Form::text('minAct', null, array('placeholder' => '0')) }}
			</div>
		</div>
		<div class="row">
			<div class="large-7 columns">
				{{ Form::label('minSat', 'Minimum SAT Score *') }}
			</div> 
				
			<div class="large-2 columns">
				{{ Form::text('minSat', null, array('placeholder' => '0')) }}
			</div>
		</div>
		<div class="row">
			<div class="large-7 columns">
				{{ Form::label('minGpa', 'Minimum Grade Point Average (4.0 Scale) *') }}
			</div>
			<div class="large-2 columns">
				{{ Form::text('minGpa', null, array('placeholder' => '0')) }}
			</div>
		</div>
		<div class="row">
			<div class="large-7 columns">
				{{ Form::label('maxGpa', 'Maximum Grade Point Average (4.0 Scale) *') }}
			</div>
			<div class="large-2 columns">
				{{ Form::text('maxGpa', null, array('placeholder' => '0')) }}
			</div>
		</div>
		<div class="row">
			<div class="large-7 columns">
				{{ Form::label('minClassRank', 'Minimum Class Rank') }}
			</div>
			<div class="large-2 columns">
				{{ Form::text('minClassRank', null, array('placeholder' => '0')) }}
			</div>
		</div>
		<div class="row">
			<div class="large-7 columns">
				{{ Form::label('minGedScore', 'Minimum GED Score *', array('class' => '')) }}
			</div>
			<div class="large-2 columns">
				{{ Form::text('minGedScore', null, array('placeholder' => '0' ,'class' => '')) }}
			</div>
		</div>
		<div class="row">
			<div class="large-4 columns">
				{{ Form::label('gender', 'Gender', array('class' => '')) }}
			</div>				
			<div class="large-8 columns">
				{{ Form::select('gender', array('anygender' => 'Open to both Males and Females', 'female' => 'Open to Females Only', 'male' => 'Open to Males Only') ) }}
			</div>
		</div>
		<div class="row">
			<div class="large-7 columns">
				{{ Form::label('militaryVetAffilation', 'Military Veteran Affiliation' ) }}
			</div>
            <div class='large-2 column'>
                <span data-tooltip aria-haspopup="true" class="has-tip" style="font-size: 15pt;" title="Students must have affiliation with the Military">&#9432;</span>
            </div>
			<div class="large-2 columns">
				{{ Form::checkbox('militaryVetAffilation','true') }}
			</div>
		</div>
		<div class="row">
			<div class="large-10 columns">
				{{ Form::label('citizenshipStatus', 'United States Citizenship Required' ) }}
			</div>
			<div class="large-2 columns">
				{{ Form::checkbox('citizenshipStatus', 'true' ) }}
			</div>
		</div>
		<div class="row">
			<div class="large-4 columns">
				{{ Form::label('minAge', 'Minimum Age *') }}
			</div> 
				
			<div class="large-2 columns">
				{{ Form::text('minAge', null, array('placeholder' => '0')) }}
			</div>
		</div>
		<div class="row">
			<div class="large-4 columns">
				{{ Form::label('maxAge', 'Maximum Age *') }}
			</div> 
			<div class="large-2 columns">
				{{ Form::text('maxAge', null, array('placeholder' => '0')) }}
			</div>
		</div>
	</div>
</div>
<div class='row'><!-- Blue button modal click area -->
	<div class='large-centered large-6 end column'>
		<div class="row filterArea">
			<div class="text-center filter-criteria">
				<div class='ajaxCallbutton' data-type='academicMajor' data-active='empty'>Academic Major</div>
				<div class='savedDropDown'></div>
			</div>
			<div class="text-center filter-criteria">
				<div class='ajaxCallbutton' data-type='artisticAbility' data-active='empty'>Artistic Ability</div>
				<div class='savedDropDown'></div>
			</div>
			<div class="text-center filter-criteria">
				<div class='ajaxCallbutton' data-type='athleticAbility' data-active='empty'>Athletic Ability</div>
				<div class='savedDropDown'></div>
			</div>
			<div class="text-center filter-criteria">
				<div class='ajaxCallbutton' data-type='attendanceState' data-active='empty'>Attendance State</div>
				<div class='savedDropDown'></div>
			</div>
			<div class="text-center filter-criteria">
				<div class='ajaxCallbutton' data-type='attendanceCollege' data-active='empty'>Attendance College</div>
				<div class='savedDropDown'></div>
			</div>
			<div class="text-center filter-criteria">
				<div class='ajaxCallbutton' data-type='ethnicity' data-active='empty'>Ethnicity</div>
				<div class='savedDropDown'></div>
			</div>
			<div class="text-center filter-criteria">
				<div class='ajaxCallbutton' data-type='honorOrganization' data-active='empty'>Honor Organization</div>
				<div class='savedDropDown'></div>
			</div>
			<div class="text-center filter-criteria">
				<div class='ajaxCallbutton' data-type='disabilities' data-active='empty'>Disabilities</div>
				<div class='savedDropDown'></div>
			</div>
			<div class="text-center filter-criteria">
				<div class='ajaxCallbutton' data-type='religion' data-active='empty'>Religion</div>
				<div class='savedDropDown'></div>
			</div>
			<div class="text-center filter-criteria">
				<div class='ajaxCallbutton' data-type='residenceCounty' data-active='empty'>County of Residence</div>
				<div class='savedDropDown'></div>
			</div>
			<div class="text-center filter-criteria">
				<div class='ajaxCallbutton' data-type='residenceState' data-active='empty'>State of Residence</div>
				<div class='savedDropDown'></div>
			</div>
			<div class="text-center filter-criteria">
				<div class='ajaxCallbutton' data-type='yearofFinancialNeed' data-active='empty'>Year of Financial Need</div>
				<div class='savedDropDown'></div>
			</div>
			<div class="text-center filter-criteria">
				<div class='ajaxCallbutton' data-type='studentsCurrentSchoolYear' data-active='empty'>Student's Current School Year</div>
				<div class='savedDropDown'></div>
			</div>
			<div class="text-center filter-criteria end">
				<div class='ajaxCallbutton' data-type='specialMiscCriteria' data-active='empty'>Special/Misc. Criteria</div>
				<div class='savedDropDown'></div>
			</div>
		</div>
	</div>
	<br/><br/>
</div>
<div class='row'>
	<div class='small-centered text-center small-12 medium-6 column'>
		{{ Form::submit('Submit', array('class'=>'button')) }}
	</div>
</div>
{{ Form::close() }}
<div id="scholarshipModal" class="reveal-modal" data-reveal>
	<div class='row'>
		<div class='column'>
			<h2>Academic Major</h2>
		</div>
	</div>
	<div class='row'>
		<div class='column'>
			<p></p>
		</div>
	</div>
	<div class='row'>
		<div class='small-12 column dropdownarea' id='majorOptions'>
			<!-- Drop down auto fill will go here.-->
		</div>
	</div>
	<br/>
	<div class='row'>
		<div class='small-6 column close-reveal-modal'>
			<div class='button scholarshipSub_modal_cancel'>Cancel</div>
		</div>
		<div class='small-6 column'>
			<div class='button modalFormSubmit'>Submit</div>
		</div>
	</div>
</div>
<!-- screw around area below-->
<style type="text/css">
	#scholarshipModal .formTitle {
    text-align: center;
    margin-bottom: 10px
    }

	#scholarshipModal .hidden {
    display: none
    }

	#scholarshipModal .formTitle div {
    margin: 3px;
    width: 61px;
    }

	#scholarshipModal input {
    display: inline
    }

	#scholarshipModal label {
    display: inline
    }

	#scholarshipModal .checkboxwrapper {
    display: none;
    float: left;
    width: 50%
    }

	#scholarshipModal .checkboxwrapper.active {
    display: inline-block
    }

	.ajaxCallbutton {
    background-color: #008CBA;
    border-color: #007095;
    border-style: solid;
    border-width: 0;
    color: #FFF;
    cursor: pointer;
    display: inline-block;
    font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
    font-size: 1rem;
    font-weight: normal;
    line-height: normal;
    padding: 1rem 2rem 1.0625rem;
    position: relative;
    text-align: center;
    text-decoration: none;
    -moz-transition: background-color 300ms ease-out 0s;
    -o-transition: background-color 300ms ease-out 0s;
    -webkit-transition: background-color 300ms ease-out 0s;
    transition: background-color 300ms ease-out 0s;
    -moz-border-radius: 7px;
    -webkit-border-radius: 7px;
    border-radius: 7px;
    width: 100%
    }

	dialog, .reveal-modal {
    	width: 60%
    }
    @media only screen and (max-width: 40em){
    	dialog, .reveal-modal {
	    	width: 100%
	    }
    }

	.checkboxwrapper {
    display: inline-block
    }

	.savedDropDown input {
    display: none;
    overflow: hidden
    }

	.savedDropDown .formTitle {
    display: none
    }

	.filled {
    margin-bottom: 0
    }

	.savedDropDown .checkboxwrapper {
    /*border-bottom: 3px dotted #808080;*/
    display: block;
    padding-bottom: 14px;
    padding-top: 14px
    }

	.filter-criteria{
		width: 47.5%;
		margin: 5px 5px;
	}
</style>
@stop
