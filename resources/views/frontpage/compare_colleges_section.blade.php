{{Form::open(array('action' =>'SearchController@index', 'data-abide', 'method' => 'get'))}}
<div class="row">
	<div class="column small-12 medium-9 large-7 make-room-for-signedin-topbar" style="margin-bottom: 2em;">
		<div class="row battle-header">
			<div class="column small-3">
				<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/boxing-gloves-large-compare-section.png" width="150" height="85" alt="">
			</div>
			<div class="column small-9">
				<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/battle-schools-text.png" width="368" height="61" alt="">
			</div>
		</div>
		<div class="row battle-search-row">
			<div class="column small-12">
				<div class="battle-search-bar-container">
					{{Form::text('battle_search_1', null, array('placeholder'=>'Start typing a school name...', 'onKeyPress'=>"comparisionAutocomplete('battle_search_1','college_slug');", 'id'=>'battle_search_1', 'class'=>'battleschools_search_input'))}}
					<div class="battle-schools-search-number text-center">1</div>
				</div>
			</div>
		</div>
		<div class="row battle-search-row">
			<div class="column small-12">
				<div class="battle-search-bar-container">
					{{Form::text('battle_search_2', null, array('placeholder'=>'Start typing a school name...', 'onKeyPress'=>"comparisionAutocomplete('battle_search_2','college_slug');", 'id'=>'battle_search_2', 'class'=>'battleschools_search_input'))}}
					<div class="battle-schools-search-number text-center">2</div>
				</div>
			</div>
		</div>

		<div class="row battle-search-row">
			<div class="column small-12">
				<div class="battle-search-bar-container">
					{{Form::text('battle_search_3', null, array('placeholder'=>'Start typing a school name...', 'onKeyPress'=>"comparisionAutocomplete('battle_search_3','college_slug');", 'id'=>'battle_search_3', 'class'=>'battleschools_search_input'))}}
					<div class="battle-schools-search-number text-center">3</div>
				</div>
			</div>
		</div>
		
		<div class="row battle-search-row">
			<div class="column small-12 large-6">
				<div class="submit_battle_search_btn text-center" onClick="submitBattleSearch('battle_search_1', 'battle_search_2', 'battle_search_3');">
					Battle!
				</div>
			</div>
		</div>
	</div>

	<!-- close icon -->
	@if( isset($signed_in) && $signed_in == 1 )
	<div class="column small-12 medium-text-right medium-2 large-2 frontpage-back-btn make-room-for-signedin-topbar show-for-medium-up">
	@else
	<div class="column small-12 medium-text-right medium-2 large-2 frontpage-back-btn close-icon-col-battle-section show-for-medium-up">
	@endif
		<!-- back/close button to close side bar sections when open - start -->
		<img class="tablet-up-back-btn" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/gray-x.png" alt="">
	</div>
</div>
{{Form::close()}}