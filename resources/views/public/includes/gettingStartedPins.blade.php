	@if(!isset($pins) || $pins['getting_started_1'] == '1')
	<div class='column small-12 medium-6 large-4'>
		<div class='gs-container'>
			<div class="gs-front text-center gs-box row collapse" data-gs_pin='1'>
				@if($currentPage == 'home')
					<!-- Close button only show on home page -->
					<span class='gs-close gs-close-x gs-close-x-dark'>
							&#10006
					</span>
				@endif
				 <img src="images/pages/indicators.png" alt="" title="" />
				 <div class="gs-header-question">How do I get <br />Recruited?</div>
				 <div class='gs-expandable'>
					 In order to be recruited by colleges you will need to enter your personal information & scores on the profile page.
					 <br>
					 <br>
					 Watch your indicators and check your Portal for colleges that want to recruit you.
				</div>
				<div class='gs-link-button'>
					<a href='/profile'>
						<div class='button gs-button'>
							Work on my Profile
						</div>
					</a>
				</div>
				 <div class='ul-button gs-expand-buttons gs-got-it'>Okay, Got it!</div>
				 <div class="button gs-button gs-expand-buttons">Tell me How!</div>
				 @if($currentPage == 'home')
					<div class='gs-close gs-close-text'>
						 <span>Don't show this message again</span>
					</div>
				 @endif
			</div>
			<!-- back side of card -->
			@include('public.includes.gettingStartedPinsBack')
		</div>
	</div>
	@endif

	@if(!isset($pins) || $pins['getting_started_2'] == '1')
	<div class='column small-12 medium-6 large-4'>
		<div class='gs-container'>
			<div class="gs-front text-center gs-box row collapse" data-gs_pin='2'>
				@if($currentPage == 'home')
					<!-- Close button only show on home page -->
					<span class='gs-close gs-close-x gs-close-x-dark'>
							&#10006
					</span>
				@endif
				<img src="images/pages/school-page-help.png" alt="" title="" />
				<div class="gs-header-question">How do I find <br /> college stats?</div>
				<div class='gs-expandable'>
					 You can use any of our search bars to find a school. Try the nice big one at the top of your screen that looks like the one below. Type in a school and go!
					 <img style='margin-top: 1em;' src="/images/pages/searchbar.png"/>
				</div>
				<div class='gs-link-button'>
				</div>
				<div class="button gs-button gs-expand-buttons">Tell me How!</div>
				<div class='ul-button gs-expand-buttons gs-got-it'>Okay, Got it!</div>
				 @if($currentPage == 'home')
					<div class='gs-close gs-close-text'>
						 <span>Don't show this message again</span>
					</div>
				 @endif
			</div>
			<!-- back side of card -->
			@include('public.includes.gettingStartedPinsBack')
		</div>
	</div>
	@endif

	@if(!isset($pins) || $pins['getting_started_3'] == '1')
	<div class='column small-12 medium-6 large-4'>
		<div class='gs-container'>
			<div class="gs-front text-center gs-box row collapse" data-gs_pin='3'>
				@if($currentPage == 'home')
					<!-- Close button only show on home page -->
					<span class='gs-close gs-close-x gs-close-x-dark'>
							&#10006
					</span>
				@endif
				 <img src="images/pages/collegelist.png" alt="" title="" />
				 <div class="gs-header-question">How do I start my <br /> research?</div>
				 <a href='/college'>
					 <div class="button gs-button">Go to College Home</div>
				 </a>
				 @if($currentPage == 'home')
					<div class='gs-close gs-close-text'>
						 <span>Don't show this message again</span>
					</div>
				 @endif
			</div>  
			<!-- back side of card -->
			@include('public.includes.gettingStartedPinsBack')
		</div>
	</div>
	@endif

	<!--
	(at)if(!isset($pins) || $pins['getting_started_4 == '1')
	<div class='column small-12 medium-6 large-4'>
		<div class='gs-container'>
			<div class="gs-front text-center gs-box row collapse" data-gs_pin='4'>
				@if($currentPage == 'home')
				-->
					<!-- Close button only show on home page -->
					<!--
					<span class='gs-close gs-close-x gs-close-x-dark'>
							&#10006
					</span>
				@endif
				 <img src="images/pages/ranking.png" alt="" title="" />
				 <div class="gs-header-question">Where do I find <br />college rankings?</div>
				 <a href='/ranking'>
					 <div class="button gs-button">See full Rankings</div>
				 </a>
				 @if($currentPage == 'home')
					<div class='gs-close gs-close-text'>
						 <span>Don't show this message again</span>
					</div>
				 @endif
			</div>
			-->
			<!-- back side of card -->
			<!--
			(at)include('public.includes.gettingStartedPinsBack')
		</div>
	</div>
	(at)endif
	-->

	@if(!isset($pins) || $pins['getting_started_5'] == '1')
	<div class='column small-12 medium-6 large-4'>
		<div class='gs-container'>
			<div class="gs-front text-center gs-box row collapse" data-gs_pin='5'>
				@if($currentPage == 'home')
					<!-- Close button only show on home page -->
					<span class='gs-close gs-close-x gs-close-x-dark'>
							&#10006
					</span>
				@endif
				 <img src="images/pages/battle.png" alt="" title="" />
				 <div class="gs-header-question">How do I <br />compare colleges?</div>
				 <a href='/comparison'>
					 <div class="button gs-button">BATTLE<span class="f-normal"> SCHOOLS!</span></div>
				 </a>
				 @if($currentPage == 'home')
					<div class='gs-close gs-close-text'>
						 <span>Don't show this message again</span>
					</div>
				 @endif
			</div>
			<!-- back side of card -->
			@include('public.includes.gettingStartedPinsBack')
		</div>
	</div>
	@endif

	@if(!isset($pins) || $pins['getting_started_6'] == '1')
	<div class='column small-12 medium-6 large-4'>
		<div class='gs-container'>
			<div class="gs-front text-center gs-box row collapse" data-gs_pin='6'>
				@if($currentPage == 'home')
					<!-- Close button only show on home page -->
					<span class='gs-close gs-close-x gs-close-x-dark'>
							&#10006
					</span>
				@endif
				 <img src="images/pages/portal.png" alt="" title="" />
				 <div class="gs-header-question">How do I engage <br />with colleges?</div>
				 <a href='/portal'>
					 <div class="button gs-button">Go to my Portal</div>
				 </a>
				 @if($currentPage == 'home')
					<div class='gs-close gs-close-text'>
						 <span>Don't show this message again</span>
					</div>
				 @endif
			</div>
			<!-- back side of card -->
			@include('public.includes.gettingStartedPinsBack')
		</div>
	</div>
	@endif

	<!--
	if(!isset($pins) || $pins['getting_started_7'] == '1')
	HIDE THIS PIN FOR NOW
	-->
	@if(1 === 2)
	<div class='column small-12 medium-6 large-4'>
		<div class='gs-container'>
			<div class="gs-front text-center gs-box row collapse" data-gs_pin='7'>
				@if($currentPage == 'home')
					<!-- Close button only show on home page -->
					<span class='gs-close gs-close-x gs-close-x-dark'>
							&#10006
					</span>
				@endif
				<img src="images/pages/portal.png" alt="" title="" />
				<div class="gs-header-question">Where can I see my <br />scholarships?</div>
				 <div class='gs-expandable'>
					Fill out your Personal Info & Accomplishments sections in your profile so we can make personalized recommendations on scholarships for you.
					<br>
					<br>
					Check for new scholarships in your Portal!
				</div>
				<div class='gs-link-button'>
					<a href='#'>
						<div class='button gs-button'>
							View Scholarships
						</div>
					</a>
				</div>
				 <div class="button gs-button gs-expand-buttons">Tell me How!</div>
				 <div class='ul-button gs-expand-buttons gs-got-it'>Okay, Got it!</div>
				 @if($currentPage == 'home')
					<div class='gs-close gs-close-text'>
						 <span>Don't show this message again</span>
					</div>
				 @endif
			</div>
			<!-- back side of card -->
			@include('public.includes.gettingStartedPinsBack')
		</div>
	</div>
	@endif

	@if(!isset($pins) || $pins['getting_started_8'] == '1')
	<div class='column small-12 medium-6 large-4'>
		<div class='gs-container'>
			<div class="gs-front text-center gs-box row collapse" data-gs_pin='8'>
				@if($currentPage == 'home')
					<!-- Close button only show on home page -->
					<span class='gs-close gs-close-x gs-close-x-dark'>
							&#10006
					</span>
				@endif
				 <img src="images/pages/portal.png" alt="" title="" />
				 <div class="gs-header-question">Where can I find <br />helpful articles?</div>
				 <a href='/news'>
					 <div class="button gs-button">Go to News</div>
				 </a>
				 @if($currentPage == 'home')
					<div class='gs-close gs-close-text'>
						 <span>Don't show this message again</span>
					</div>
				 @endif
			</div>
			<!-- back side of card -->
			@include('public.includes.gettingStartedPinsBack')
		</div>
	</div>
	@endif

	@if(!isset($pins) || $pins['getting_started_9'] == '1')
	<div class='column small-12 medium-6 large-4'>
		<div class='gs-container'>
			<div class="gs-front text-center gs-box row collapse" data-gs_pin='9'>
				@if($currentPage == 'home')
					<!-- Close button only show on home page -->
					<span class='gs-close gs-close-x gs-close-x-dark'>
							&#10006
					</span>
				@endif
				 <img src="images/pages/news.png" alt="" title="" />
				 <div class="gs-header-question">How do I stay updated<br /> on colleges I like?</div>
				 <div class='gs-expandable'>
					As you add schools to your list from the Portal, your feed will be updated with personalized information from those schools.
				</div>
				 <div class="button gs-button gs-expand-buttons">Tell me How!</div>
				 <div class='ul-button gs-expand-buttons gs-got-it'>Okay, Got it!</div>
				 @if($currentPage == 'home')
					<div class='gs-close gs-close-text'>
						 <span>Don't show this message again</span>
					</div>
				 @endif
			</div>
			<!-- back side of card -->
			@include('public.includes.gettingStartedPinsBack')
		</div>
	</div>
	<div id='card_delay' class='column small-12 medium-6 large-4' data-card_delay='' style='display: none;'></div>
	@endif
