@extends('public.footerpages.master')
@section('help_heading')
	{{$help_heading or ''}}
@stop

@section('content')
<?php 
	$fetchrow = '';
?>
		<div class="small-12 medium-4 column">
			<div class="faq-questions" data-magellan-expedition>
			   <ul>
                @if (isset($faq_question_heading) && $faq_question_heading == 'PRIVACY')
                    <li class='mb10'><b>Privacy and Data Rights FAQ:</b></li>
                @else
				    <li class='mb10'><b>{{ $faq_question_heading }} FAQ:</b></li>
                @endif
					@if(count($faq)>0)
						@foreach($faq as $faq)
						<li class="faq-q-link">
							<dd data-magellan-arrival="{{ $faq['anchor'] }}">
								<a href='#{{ $faq["anchor"] }}'>
									{{$faq['question']}}
								</a>
							</dd>
						</li>
						<?php
							$fetchrow.='
								<div class="row">
									<div class="small-12 column">
										<a name="' . $faq['anchor'] . '"></a>
										<span class="faq-q" data-magellan-destination="' . $faq['anchor'] . '">Q: ' . $faq['question'] . '</span>
									</div>
								</div>
								<div class="row">
									<div class="small-12 column">
										<p class="faq-a"><b class="bold-letter-a">A</b>: ' . $faq['answer'] . '</p>
									</div>
								</div>
									';
						?>                                 
						@endforeach
					@endif
				</ul>
			</div>


	@if($faq['type']=='internship')
	<!-- HIDE THIS SECTION FOR NOW UNTIL WE HAVE DOWNLOADS -->
	<!--
	<div class="faq-downloads">
		<b>DOWNLOADS & DOCUMENTS :</b><br />
		<ul class="pt10">
			<li class="pb5"><img src="images/nav-icons/white-down-arrow.png" alt="" title="" /> Media kit (.zip)</li>
			<li class="pb5"><img src="images/nav-icons/white-down-arrow.png" alt="" title="" /> Brochure (.pdf)</li>
			<li class="pb5"><img src="images/nav-icons/white-down-arrow.png" alt="" title="" /> Become an intern (.pdf)</li>
		</ul>
	</div>
	-->
	@endif

	</div>
	<div class="small-12 medium-8 column faq-answers">
        @if (isset($faq_question_heading) && $faq_question_heading == 'PRIVACY')
            <div class='privacy-faqs-intro'>
                <div>We believe that users should be treated equally no matter where they are, and so we are making the following options to control your data available to all users, regardless of their location</div>
                <div class='mt10'>You can update certain information by accessing your profile via the “Me” tab. You can also unsubscribe from certain emails by clicking the “unsubscribe” link they contain</div>
                <div class='mt10 mb10'>Individuals in the EU have certain legal rights to obtain confirmation of whether we hold personal data about them, to access personal data we hold about them (including, in some cases, in portable form), and to obtain its correction, update, amendment or deletion in appropriate circumstances. They may also object to our uses or disclosures of personal data, to request a restriction on its processing, or withdraw any consent, though such actions typically will not have retroactive effect. They also will not affect our ability to continue processing data in lawful ways.</div>
            </div>
        @endif
    	{!! $fetchrow !!}
        @if (isset($faq_question_heading) && $faq_question_heading == 'PRIVACY')
            <div class='privacy-faqs-outro'>The rights and options described above are subject to limitations and exceptions under applicable law. In addition to those rights, you have the right to lodge a complaint with the relevant supervisory authority. However, we encourage you to contact us first, and we will do our very best to resolve your concern.</div>
        	</div>
        @endif
    </div>

<div class="clearfix"></div>
@stop
