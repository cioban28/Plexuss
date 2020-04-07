@extends('private.ranking.master')

@section('sidebar')
{{-- */$ranking_degree=isset($QueryStrings['ranking_degree'])?$QueryStrings['ranking_degree']:''/* --}}
{{-- */$ranking_school_sector=isset($QueryStrings['ranking_school_sector'])?$QueryStrings['ranking_school_sector']:''/* --}}
{{-- */$ranking_source=isset($QueryStrings['ranking_source'])?$QueryStrings['ranking_source']:''/* --}}
{{-- */$campus_settings=isset($QueryStrings['campus_settings'])?$QueryStrings['campus_settings']:''/* --}}
{{-- */$religious_affiliation=isset($QueryStrings['religious_affiliation'])?$QueryStrings['religious_affiliation']:''/* --}}
{{-- */$ranking_state=isset($QueryStrings['ranking_state'])?$QueryStrings['ranking_state']:''/* --}}
{{-- */$campus_housing=isset($QueryStrings['campus_housing'])?'checked':''/* --}}

{{-- */$tuition_fee_max=isset($QueryStrings['tuition_fee_max'])?$QueryStrings['tuition_fee_max']:''/* --}}
{{-- */$tuition_fee_min=isset($QueryStrings['tuition_fee_min'])?$QueryStrings['tuition_fee_min']:''/* --}}
{{-- */$undergrade_max=isset($QueryStrings['undergrade_max'])?$QueryStrings['undergrade_max']:''/* --}}
{{-- */$undergrade_min=isset($QueryStrings['undergrade_min'])?$QueryStrings['undergrade_min']:''/* --}}
{{-- */$admitted_max=isset($QueryStrings['admitted_max'])?$QueryStrings['admitted_max']:''/* --}}
{{-- */$admitted_min=isset($QueryStrings['admitted_min'])?$QueryStrings['admitted_min']:''/* --}}
    <div class='row'>
        <div class='column small-12 ranking_listing_search' id="RankingSearchPanel">
				{{ Form::open(array('action' =>'RankingController@listing', 'id'=>'ranking-search-form','method'=>'get')) }}
				<div class="row show-for-small-only">
					<div class="small-8 columns" style="font-size:23px;line-height:45px;color:#F3F3F3;">Plexuss Rankings</div>
					<div class="small-4 columns" style="font-size:18px;line-height:45px;color:#797979;text-align:right;padding-top:11px;cursor:pointer;" onclick="HideRankingFilters();"><img src="/images/ranking/filter-close-icon.png" alt=""/></div>
				</div>
				<div class="row">
					<div class="large-12 columns page-right-side-bar side-bar-2 radius-4" style="font-size:16px;color:#ffffff;font-weight:bold;text-align:center;padding:15px 0px;">
							<div class='row'>
								<div class='small-12 column'>
									<div style="text-align:left; margin-bottom: 1em;">
										Filter Plexuss Rankings:
									</div>
								</div>
							</div>
							<!--//////////////////// SCHOOL NAME \\\\\\\\\\\\\\\\\\\\ -->
							<div class='row'>
								<div id='school_name_row' class='column small-12 ranking-search-spacing'>
									{{ Form::label('school_name_autocomp', 'School Name') }}
									<div id='school_name_tag_wrapper' class='TagWrapper'>
										<!-- Tag container -->
										<div id='school_name_tag_display' class='tag_display'>
										</div>
											{{ Form::text('school_name_autocomp', '', array( 'id' => 'school_name_autocomp', 'class' => 'tag_autocomp', 'placeholder' => 'Search for a school' )) }}
											<!-- add state data here -->
											{{ Form::hidden('school_name_tag_list', '', array( 'id' => 'school_name_tag_list', 'data-tags' => isset( $school_name_tag_list_json ) ? $school_name_tag_list_json : '', 'data-default_value' => 'slug')) }}
											{{ Form::hidden('school_name_tag_list_json', '', array( 'id' => 'school_name_tag_list_json')) }}
									</div>
								</div>
							</div>
							<!--\\\\\\\\\\\\\\\\\\\\ SCHOOL NAME //////////////////// -->
							<!--//////////////////// STATE \\\\\\\\\\\\\\\\\\\\ -->
							<div class="row">
								<div id='state_row' class='column small-12 ranking-search-spacing'>
									{{ Form::label( 'state_autocomp', 'State' ) }}
									<div id='state_tag_wrapper' class='TagWrapper'>
										<div id='state_tag_display' class='tag_display'>
										</div>
										{{ Form::text('state_autocomp', '', array( 'id' => 'state_autocomp', 'class' => 'tag_autocomp', 'placeholder' => 'Search for a state' )) }}
										<!-- add state data here -->
										{{ Form::hidden('state_tag_list', '', array( 'id' => 'state_tag_list', 'data-tags' => isset( $state_tag_list_json ) ? $state_tag_list_json : '', 'data-default_value' => 'value')) }}
										{{ Form::hidden('state_tag_list_json', '', array( 'id' => 'state_tag_list_json')) }}
									</div>
								</div>
							</div>
							<!--\\\\\\\\\\\\\\\\\\\\ STATE //////////////////// -->
							<div class='row'>
								<!--//////////////////// ZIP \\\\\\\\\\\\\\\\\\\\ -->
								<div class='column small-5 ranking-search-spacing'>
									{{ Form::label( 'ranking_zip', 'Zip Code') }}
									<input type="text"  name="ranking_zip" id="ranking_zip" class="ranking-search-text ranking-search-form-fields" placeholder="" value="{{{$QueryStrings['ranking_zip'] or ""}}}" style="width:88%;">
								</div>
								<!--\\\\\\\\\\\\\\\\\\\\ ZIP //////////////////// -->
								<!--//////////////////// ZIP DISTANCE \\\\\\\\\\\\\\\\\\\\ -->
								<div class='column small-7 ranking-search-spacing' style="padding-left:6px;">
									<label style="font-size:10px;padding-bottom:10px;padding-top: 5px;">Within <span id="ranking-search-slider-range-label"></span> miles</label>
									<div id="ranking-search-slider-range" style="padding-left:5px;"></div>
									<input type="hidden" name="ranking_search_zip_max" id="ranking_search_zip_max" value="{{$ranking_search_zip_max}}" /><input type="hidden" name="ranking_search_zip_min" id="ranking_search_zip_min" value="{{$ranking_search_zip_min}}" />
								</div>
								<!--\\\\\\\\\\\\\\\\\\\\ ZIP DISTANCE //////////////////// -->
							</div>
							<!--//////////////////// DEGREE TYPE \\\\\\\\\\\\\\\\\\\\ -->
							<div class='row'>
								<div class='column small-12 ranking-search-spacing'>
									{{ Form::label( 'degree_select', 'Degree Type' ) }}
									<div id='degree_tag_wrapper' class='TagWrapper'>
										<!-- Tag container -->
										<div id='degree_tag_display' class='tag_display'>
										</div>
											{{ Form::select('degree_select', $degree_select, '', array( 'id' => 'degree_select', 'class' => 'tag_autocomp', 'placeholder' => 'Search for a state' )) }}
											<!-- add state data here -->
											{{ Form::hidden('degree_tag_list', '', array( 'id' => 'degree_tag_list', 'data-tags' => isset( $degree_tag_list_json ) ? $degree_tag_list_json : '', 'data-default_value' => 'id')) }}
											{{ Form::hidden('degree_tag_list_json', '', array( 'id' => 'degree_tag_list_json')) }}
									</div>
								</div>
							</div>
							<!--\\\\\\\\\\\\\\\\\\\\ DEGREE TYPE //////////////////// -->
							<!--//////////////////// INSTITUTION TYPE \\\\\\\\\\\\\\\\\\\\ -->
							<div class='row'>
								<div class='column small-12 ranking-search-spacing'>
									{{ Form::label( 'school_sector_select', 'Institution Type') }}
									<div id='school_sector_tag_wrapper' class='TagWrapper'>
										<!-- Tag container -->
										<div id='school_sector_tag_display' class='tag_display'>
										</div>
											{{ Form::select('school_sector_select', $school_sector_select, '', array( 'id' => 'school_sector_select', 'class' => 'tag_autocomp', 'placeholder' => 'Search for a state' )) }}
											<!-- add state data here -->
											{{ Form::hidden('school_sector_tag_list', '', array( 'id' => 'school_sector_tag_list', 'data-tags' => isset( $school_sector_tag_list_json ) ? $school_sector_tag_list_json : '', 'data-default_value' => 'id')) }}
											{{ Form::hidden('school_sector_tag_list_json', '', array( 'id' => 'school_sector_tag_list_json')) }}
									</div>
								</div>
							</div>
							<!--\\\\\\\\\\\\\\\\\\\\ INSTITUTION TYPE //////////////////// -->
							<!--//////////////////// RANKING SOURCE \\\\\\\\\\\\\\\\\\\\ -->
							<div class='row'>
								<div class='column small-12 ranking-search-spacing'>
									{{ Form::label( 'ranking_source_select', 'Ranking Source' ) }}
									<div id='ranking_source_tag_wrapper' class='TagWrapper'>
										<!-- Tag container -->
										<div id='ranking_source_tag_display' class='tag_display'>
										</div>
											{{ Form::select('ranking_source_select', $ranking_source_select, '', array( 'id' => 'ranking_source_select', 'class' => 'tag_autocomp', 'placeholder' => 'Search for a state' )) }}
											<!-- add state data here -->
											{{ Form::hidden('ranking_source_tag_list', '', array( 'id' => 'ranking_source_tag_list', 'data-tags' => isset( $ranking_source_tag_list_json ) ? $ranking_source_tag_list_json : '', 'data-default_value' => 'id')) }}
											{{ Form::hidden('ranking_source_tag_list_json', '', array( 'id' => 'ranking_source_tag_list_json')) }}
									</div>
								</div>
							</div>
							<!--\\\\\\\\\\\\\\\\\\\\ RANKING SOURCE //////////////////// -->
						<!-- MORE FILTER OPTIONS -->
						<div class='row'>
							<div class="column small-12 ranking-search-more-filters ranking-search-spacing" style="cursor:pointer;" onclick="$('#Ranking_MoreFilters').slideToggle( 250, 'easeInOutExpo' );">
							+ more filter options
							</div>
						</div>
						<!-- MORE FILTER OPTIONS -->
						<!--//////////////////// MORE FILTER OPTIONS ROW \\\\\\\\\\\\\\\\\\\\-->
						<div class='row'>
							<div class="small-12 columns" id="Ranking_MoreFilters" style="padding:0px;display:{{$displayMoreFilter}};">
								<div class='row'>
									<div class="small-12 columns ranking-search-spacing"><label>Housing? &nbsp;&nbsp;<input type="checkbox" name="campus_housing" id="campus_housing" value="Yes" {{$campus_housing or ''}} />&nbsp;Yes</label></div>
								</div>
								<!--//////////////////// CAMPUS SETTING \\\\\\\\\\\\\\\\\\\\ -->
								<div class='row'>
									<div class='column small-12 ranking-search-spacing'>
										{{ Form::label( 'campus_setting_select', 'Campus Setting' ) }}
										<div id='campus_setting_tag_wrapper' class='TagWrapper'>
											<!-- Tag container -->
											<div id='campus_setting_tag_display' class='tag_display'>
											</div>
												{{ Form::select('campus_setting_select', $campus_setting_select, '', array( 'id' => 'campus_setting_select', 'class' => 'tag_autocomp', 'placeholder' => 'Search for a state' )) }}
												<!-- add state data here -->
												{{ Form::hidden('campus_setting_tag_list', '', array( 'id' => 'campus_setting_tag_list', 'data-tags' => isset( $campus_setting_tag_list_json ) ? $campus_setting_tag_list_json : '', 'data-default_value' => 'id')) }}
												{{ Form::hidden('campus_setting_tag_list_json', '', array( 'id' => 'campus_setting_tag_list_json')) }}
										</div>
									</div>
								</div>
								<!--\\\\\\\\\\\\\\\\\\\\ CAMPUS SETTING //////////////////// -->
								<!--//////////////////// RELIGIOUS AFFILIATION \\\\\\\\\\\\\\\\\\\\ -->
								<div class='row'>
									<div class='column small-12 ranking-search-spacing'>
										{{ Form::label( 'religious_affiliation_autocomp', 'Religious Affiliation' ) }}
										<div id='religious_affiliation_tag_wrapper' class='TagWrapper'>
											<!-- Tag container -->
											<div id='religious_affiliation_tag_display' class='tag_display'>
											</div>
												{{ Form::text('religious_affiliation_autocomp', '', array( 'id' => 'religious_affiliation_autocomp', 'class' => 'tag_autocomp', 'placeholder' => 'Search for religious affiliation' )) }}
												<!-- add state data here -->
												{{ Form::hidden('religious_affiliation_tag_list', '', array( 'id' => 'religious_affiliation_tag_list', 'data-tags' => isset( $religious_affiliation_tag_list_json ) ? $religious_affiliation_tag_list_json : '', 'data-default_value' => 'value')) }}
												{{ Form::hidden('religious_affiliation_tag_list_json', '', array( 'id' => 'religious_affiliation_tag_list_json')) }}
										</div>
									</div>
								</div>
								<!--\\\\\\\\\\\\\\\\\\\\ RELIGIOUS AFFILIATION //////////////////// -->
								<!-- MAX TUITION AND FEES -->
								<div class='row'>
									<div class='column small-12 ranking-search-spacing'>
										<label>Maximum Tuition &amp; Fees</label>
										<div id="ranking-search-tutitionfee-rangeslider" style="margin:5px;"></div>
										<label id="ranking-search-tutitionfee-rangeslider-label">(0- $45,000)</label>
										<input type="hidden" name="tuition_fee_max" id="tuition_fee_max" value="{{$tuition_fee_max or ''}}" /><input type="hidden" name="tuition_fee_min" id="tuition_fee_min" value="{{$tuition_fee_min or ''}}" />
									</div>
								</div>
								<!-- MAX TUITION AND FEES -->
								<!-- UNDERGRADUATE ENROLLMENT -->
								<div class='row'>
									<div class='column small-12 ranking-search-spacing'>
										<label>Undergraduate Enrollment</label>
										<div id="ranking-search-undergraduate-rangeslider" style="margin:5px;"></div>
										<label id="ranking-search-undergraduate-rangeslider-label">($3,000 - $50,000)</label>
										<input type="hidden" name="undergrade_max" id="undergrade_max" value="{{$undergrade_max or ''}}" /><input type="hidden" name="undergrade_min" id="undergrade_min" value="{{$undergrade_min or ''}}" />
									</div>
								</div>
								<!-- UNDERGRADUATE ENROLLMENT -->
								<!-- APPLICANTS ADMITTED -->
								<div class='row'>
									<div class='column small-12 ranking-search-spacing'>
										<label>% Applicants Admitted</label>
										<div id="ranking-search-applicantadmitted-rangeslider" style="margin:5px;"></div>
										<label id="ranking-search-applicantadmitted-rangeslider-label">(25% - 90%)</label>
										<input type="hidden" name="admitted_max" id="admitted_max" value="{{$admitted_max or ''}}" /><input type="hidden" name="admitted_min" id="admitted_min" value="{{$admitted_min or ''}}" />
									</div>
								</div>
								<!-- APPLICANTS ADMITTED -->
								<div class='row'>
									<div class='column small-12 ranking-search-spacing'>
									<label>Test Scores 25th Percentile</label>
									</div>
								</div>

								<div class='row'>
									<div class="small-12 columns">
									<span style="display:inline-block;width:109px;">{{ Form::label( 'sat_read_min', 'Critical Reading' ) }}</span>
									<span><input type="text" name="sat_read_min" id="sat_read_min" class="ranking-search-text ranking-search-form-fields" style="width:35px;display:inline-block;" value="{{{$QueryStrings['sat_read_min'] or ""}}}" /></span>
									<span style="display:inline-block;">-</span>
									<span><input type="text" name="sat_read_max" id="sat_read_max" class="ranking-search-text ranking-search-form-fields" style="width:35px;display:inline-block;" value="{{{$QueryStrings['sat_read_max'] or ""}}}" /></span>
									</div>
								</div>

								 <div class='row'>
									 <div class="small-12 columns">
									<span style="display:inline-block;width:109px;">{{ Form::label( 'sat_math_min', 'SAT Math' ) }}</span>
									<span><input type="text" name="sat_math_min" id="sat_math_min" class="ranking-search-text ranking-search-form-fields" style="width:35px;display:inline-block;" value="{{{$QueryStrings['sat_math_min'] or ""}}}" /></span>
									<span style="display:inline-block;">-</span>
									<span><input type="text" name="sat_math_max" id="sat_math_max" class="ranking-search-text ranking-search-form-fields" style="width:35px;display:inline-block;" value="{{{$QueryStrings['sat_math_max'] or ""}}}" /></span>
									</div>
								 </div>

								<div class='row'>
									<div class="small-12 columns ">
									<span style="display:inline-block;width:109px;">{{ Form::label( 'act_composite_min', 'ACT Composite' ) }}</span>
									<span><input type="text" name="act_composite_min" id="act_composite_min" class="ranking-search-text ranking-search-form-fields" style="width:35px;display:inline-block;" value="{{{$QueryStrings['act_composite_min'] or ""}}}" /></span>
									<span style="display:inline-block;">-</span>
									<span><input type="text" name="act_composite_max" id="act_composite_max" class="ranking-search-text ranking-search-form-fields" style="width:35px;display:inline-block;" value="{{{$QueryStrings['act_composite_max'] or ""}}}" /></span>
									</div>
								</div>
							</div>
						</div>
						<!--\\\\\\\\\\\\\\\\\\\\ MORE FILTER OPTIONS ROW ////////////////////-->
						<div class="small-12 columns" style="padding:0px;padding-top:10px;">
						<div class="row">
							<div class="column small-6">
							<input type="reset" name="Clear" value="Clear" class="ranking-clear-button" onclick="window.location='/ranking/listing/';" />
							</div>
							<div class="column small-6">
							<input type="submit" name="Search" value="Search" class="ranking-search-button" />
							</div>
						</div>
						</div>

						<div class="clearfix"></div>
					</div>                
				</div>
				{{Form::close()}}
        </div>
    </div>
@stop

@section('content')
    <div class="row collapse">
    <div class="small-12 columns ranking-search-listing-main">        
        <div class="row hide-for-small-only" style="margin-bottom: 10px;">
        	<div class="small-12 columns" style="background-color:#0085b2;line-height:35px;font-size:13px;color:#FFFFFF; border-top-left-radius: 5px; border-top-right-radius: 5px;">
				<div class="row">
					<div class="column small-12 large-6">
		        		Customize this ranking list using the filter on the left!
					</div>
					<div class="column small-12 large-6 large-text-right">
						<a href="/ranking" class="how-does-rankings-work">How do our rankings work?</a>
					</div>
				</div>
        	</div>
        </div>
        <div class="row show-for-small-only">
        <div class="small-8 columns" style="font-size:23px;line-height:45px;color:#F3F3F3;">Plexuss Rankings</div>
        <div class="small-4 columns" style="font-size:18px;line-height:45px;color:#797979;text-align:right;cursor:pointer;" onclick="ShowRankingFilters();">FILTER</div>
        </div>
        <div class="row hide-for-small-only" style="padding-bottom:15px;">
        	<div class="small-6 columns" style="font-size:24px;">
				<div  style="margin-bottom: 7px;">
					Plexuss College Rankings
				</div>
				<!--/////////////// SOCIAL MEDIA BUTTONS \\\\\\\\\\\\\\\-->
				<!--
				<a class='social_share share_facebook' 
					data-params='{
						"platform":"facebook"
					}'
				></a>
				<a class='social_share share_twitter'
					data-params='{
						"platform": "twitter",
						"text": "Plexuss College Rankings"
					}'
				></a>
				-->
				<!-- Print -->
				<!--
				<a class='social_share print_me' onClick="window.print()"></a>
				-->
				<!--\\\\\\\\\\\\\\\ SOCIAL MEDIA BUTTONS ///////////////-->
            </div>
            <div class="small-6 columns" style="text-align:right;"><input type="button" name="Compare Selected School" class="compare-selected-school-button" value="Compare Selected School" onclick="CompareSchools()" /></div>
        </div>
        <div class="row collapse ranking-grid-headrow" style="position:relative;">
        	<div class="small-3 medium-3 columns">
            	<div class="row collapse hide-for-small-only">
                	<div class="small-4 columns text-center">COMPARE</div>
                    <div class="small-8 columns text-center" onClick="RankingSort('plexuss');" style="cursor:pointer;"><img src="/images/ranking/sorting-arrows.png" alt=""/>&nbsp;PLEXUSS RANKING</div>
                </div>
                <div class="row collapse show-for-small-only">
                    <div class="small-12 columns text-center" onClick="RankingSort('plexuss');" style="cursor:pointer;"><img src="/images/ranking/sorting-arrows.png" alt=""/>&nbsp;PLEXUSS RANKING</div>
                </div>
            </div>
            <div class="small-5 medium-4 columns" onClick="RankingSort('College');" style="cursor:pointer;"><img src="/images/ranking/sorting-arrows.png" alt=""/>&nbsp;SCHOOL</div>
            <div class="small-4 medium-5 columns">
            	<div class="row collapse hide-for-small-only">
                	<div class="small-4 columns text-center" id="RankingSource1" onClick="RankingSort('us_news');" style="cursor:pointer;"><img src="/images/ranking/sorting-arrows.png" alt=""/>&nbsp;U.S. NEWS</div>
                    <div class="small-4 columns text-center" id="RankingSource2" onClick="RankingSort('forbes');" style="cursor:pointer;"><img src="/images/ranking/sorting-arrows.png" alt=""/>&nbsp;FORBES</div>
                    <div class="small-4 columns text-center" id="RankingSource3" onClick="RankingSort('qs');" style="cursor:pointer;"><img src="/images/ranking/sorting-arrows.png" alt="" />&nbsp;QS WORLD</div>
                    <div class="small-4 columns text-center" id="RankingSource4" onClick="RankingSort('reuters');" style="cursor:pointer;"><img src="/images/ranking/sorting-arrows.png" alt="" />&nbsp;REUTERS</div>
                    <div class="small-4 columns ext-center" id="RankingSource5" onClick="RankingSort('shanghai_academic');" style="cursor:pointer;"><img src="/images/ranking/sorting-arrows.png" alt=""/>&nbsp;SHANGHAI ACADEMIC</div>
                    <div style="position:absolute;right: 6px;cursor:pointer;" onClick="ShowOhterSources()"><img src="/images/ranking/arrow-right.png" id="showhiderightdata" alt=""/></div>
                </div>
                
                <div class="row collapse show-for-small-only">
                	<div class="small-12 columns text-center" id="mRankingSource1" onClick="RankingSort('us_news');" style="cursor:pointer;"><img src="/images/ranking/sorting-arrows.png" alt=""/>&nbsp;U.S. NEWS</div>
                    <div class="small-12 columns text-center" id="mRankingSource2" onClick="RankingSort('forbes');" style="cursor:pointer;"><img src="/images/ranking/sorting-arrows.png" alt=""/>&nbsp;FORBES</div>
                    <div class="small-12 columns text-center" id="mRankingSource3" onClick="RankingSort('qs');" style="cursor:pointer;"><img src="/images/ranking/sorting-arrows.png" alt="" />&nbsp;QS WORLD</div>
                    <div class="small-12 columns text-center" id="mRankingSource4" onClick="RankingSort('reuters');" style="cursor:pointer;"><img src="/images/ranking/sorting-arrows.png" alt="" />&nbsp;REUTERS</div>
                    <div class="small-12 columns ext-center" id="mRankingSource5" onClick="RankingSort('shanghai_academic');" style="cursor:pointer;"><img src="/images/ranking/sorting-arrows.png" alt="" />&nbsp;SHANGHAI ACADEMIC</div>
                    <div style="position:absolute;right: 6px;cursor:pointer;" onClick="mShowOhterSources()"><img src="/images/ranking/arrow-right.png" id="mshowhiderightdata" alt=""/></div>
                </div>
                
            </div>
        </div>
        
        <?php /*?><div class="row ranking-grid-headrow" style="position:relative;">
	        <div class="small-1 columns head-columns" style="border-right: solid 1px #7A7A7A;border-left: solid 1px #3D3D3D;cursor:pointer;">COMPARE</div>
            <div class="small-2 columns head-columns" style="border-right: solid 1px #7A7A7A;cursor:pointer;" onClick="RankingSort('plexuss');"><img src="/images/ranking/sorting-arrows.png" />&nbsp;PLEXUSS RANKING</div>
            <div class="small-4 columns head-columns" style="border-right: solid 1px #7A7A7A;border-left: solid 1px #3D3D3D;cursor:pointer;" onClick="RankingSort('College');"><img src="/images/ranking/sorting-arrows.png" />&nbsp;SCHOOL</div>
            <div class="small-2 columns head-columns" style="border-left: solid 1px #3D3D3D;cursor:pointer;" onClick="RankingSort('us_news');"><img src="/images/ranking/sorting-arrows.png" />&nbsp;U.S. NEWS</div>
            <div class="small-1 columns head-columns startcol" onClick="RankingSort('forbes');" style="cursor:pointer;display:{{$display1}}"><img src="/images/ranking/sorting-arrows.png" />&nbsp;FORBES</div>
            <div class="small-2 columns head-columns startcol" onClick="RankingSort('qs');" style="cursor:pointer;display:{{$display1}}"><img src="/images/ranking/sorting-arrows.png"  />&nbsp;QS WORLD</div>
            <div class="small-2 columns head-columns endcol" onClick="RankingSort('reuters');" style="cursor:pointer;display:{{$display2}}"><img src="/images/ranking/sorting-arrows.png"  />&nbsp;REUTERS</div>
            <div class="small-2 columns head-columns endcol" onClick="RankingSort('shanghai_academic');" style="cursor:pointer;display:{{$display2}}"><img src="/images/ranking/sorting-arrows.png"  />&nbsp;SHANGHAI ACADEMIC</div>
            <div style="position:absolute;right: 6px;cursor:pointer;" onClick="ShowOhterSources()"><img src="/images/ranking/arrow-right.png" id="showhiderightdata" /></div>
        </div>
        <?php 
        */
        ?>
        @if(isset($RankingData) && count($RankingData)>0)
            @foreach($RankingData as $key=>$dataList)
            {{-- */$LogoUrl=isset($dataList->logo_url)?'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$dataList->logo_url:''/* --}}
            <div class="row ranking-grid-data collapse">
        	<div class="small-3 medium-3 columns">
            	<div class="row collapse hide-for-small-only">
                	<div class="small-4 columns text-center">
                        <input type="checkbox" name="ranking_compare[]" id="ranking_compare{{$dataList->plexuss}}" value="{{$dataList->slug}}" />
                    </div>
                    <div class="small-8 columns text-center"><div class="ranking-number-green">
                    @if($dataList->plexuss)
                    #{{$dataList->plexuss}}
                    @else
                    N/A
                    @endif
                    </div></div>
                </div>
                <div class="row collapse show-for-small-only">
                    <div class="small-12 columns text-center"><div class="ranking-number-green">
                    @if($dataList->plexuss)
                    #{{$dataList->plexuss}}
                    @else
                    N/A
                    @endif
                    </div></div>
                </div>
            </div>
            <div class="small-5 medium-4 columns hide-for-small-only">
            	<div class="small-3 columns">@if(isset($dataList->logo_url) && $dataList->logo_url!="") <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/{{$dataList->logo_url  or ''}}" alt=""/> @endif</div>
                <div class="small-9 columns school_info_text"><div style="font-size:12px;font-weight:bold;float:left;"><a href="/college/{{$dataList->slug}}">{{$dataList->school_name}}</a><br /><span style="font-size:10px;font-weight:normal;">{{$dataList->city}},{{$dataList->state}}</span></div></div>
            </div>
            <div class="small-5 medium-4 columns show-for-small-only">
                <div class="small-12 columns school_info_text"><div style="font-size:12px;font-weight:bold;float:left;"><a href="/college/{{$dataList->slug}}">{{$dataList->school_name}}</a><br /><span style="font-size:10px;font-weight:normal;">{{$dataList->city}},{{$dataList->state}}</span></div></div>
            </div>
            <div class="small-4 medium-5 columns">
            	<div class="row collapse hide-for-small-only">
                	<div class="small-4 columns text-center RankingSource1"><div class="ranking-number-gray">
                    @if($dataList->us_news)
                    #{{$dataList->us_news}}
                    @else
                    N/A
                    @endif
                    </div></div>
                    <div class="small-4 columns text-center RankingSource2"><div class="ranking-number-gray">
                    @if($dataList->forbes)
                    #{{$dataList->forbes}}
                    @else
                    N/A
                    @endif
                    </div></div>
                    <div class="small-4 columns text-center RankingSource3"><div class="ranking-number-gray">
                    @if($dataList->qs)
                    #{{$dataList->qs}}
                    @else
                    N/A
                    @endif
                    </div></div>
                    <div class="small-4 columns text-center RankingSource4" ><div class="ranking-number-gray">
                    @if($dataList->reuters)
                    #{{$dataList->reuters}}
                    @else
                    N/A
                    @endif
                    </div></div>
                    <div class="small-4 columns text-center RankingSource5" ><div class="ranking-number-gray">
                    @if($dataList->shanghai_academic)
                    #{{$dataList->shanghai_academic}}
                    @else
                    N/A
                    @endif
                    </div></div>
                </div>
                <div class="row collapse show-for-small-only">
                	<div class="small-12 columns text-center mRankingSource1"><div class="ranking-number-gray">
                    @if($dataList->us_news)
                    #{{$dataList->us_news}}
                    @else
                    N/A
                    @endif
                    </div></div>
                    <div class="small-12 columns text-center mRankingSource2"><div class="ranking-number-gray">
                    @if($dataList->forbes)
                    #{{$dataList->forbes}}
                    @else
                    N/A
                    @endif
                    </div></div>
                    <div class="small-12 columns text-center mRankingSource3"><div class="ranking-number-gray">
                    @if($dataList->qs)
                    #{{$dataList->qs}}
                    @else
                    N/A
                    @endif
                    </div></div>
                    <div class="small-12 columns text-center mRankingSource4" ><div class="ranking-number-gray">
                    @if($dataList->reuters)
                    #{{$dataList->reuters}}
                    @else
                    N/A
                    @endif
                    </div></div>
                    <div class="small-12 columns text-center mRankingSource5" ><div class="ranking-number-gray">
                    @if($dataList->shanghai_academic)
                    #{{$dataList->shanghai_academic}}
                    @else
                    N/A
                    @endif
                    </div></div>
                </div>
            </div>
        </div>
            <?php /*?><div class="row ranking-grid-data">
            <div class="small-1 columns" style="padding-top:20px;text-align:center;"><input type="checkbox" name="ranking_compare[]" id="ranking_compare{{$dataList->plexuss}}" value="{{$dataList->id}}" /></div>
            <div class="small-2 columns" style="padding-top:12px;"><div class="ranking-number-green">
            @if($dataList->plexuss)
            #{{$dataList->plexuss}}
            @else
            N/A
            @endif
            </div></div>
            <div class="small-1 columns" style="padding:0px;"><img src="{{{$LogoUrl  or ''}}}" /></div>
            <div class="small-3 columns" style="color:#0085B2;padding:10px 13px;"><div style="font-size:12px;font-weight:bold;float:left;"><a href="/college/{{$dataList->slug}}" style="color:#0085B2;">{{$dataList->school_name}}</a><br /><span style="font-size:10px;font-weight:normal;">{{$dataList->city}},{{$dataList->state}}</span></div></div>            
            <div class="small-2 columns" style="padding-top:12px;"><div class="ranking-number-gray">
            @if($dataList->us_news)
            #{{$dataList->us_news}}
            @else
            N/A
            @endif
            </div></div>
            <div class="small-1 columns startcol" style="padding-top:12px;display:{{$display1}}"><div class="ranking-number-gray">
            @if($dataList->forbes)
            #{{$dataList->forbes}}
            @else
            N/A
            @endif
            </div></div>
            <div class="small-2 columns startcol" style="padding-top:12px;display:{{$display1}}"><div class="ranking-number-gray">
            @if($dataList->qs)
            #{{$dataList->qs}}
            @else
            N/A
            @endif
            </div></div>
            
             <div class="small-2 columns endcol" style="padding-top:12px;display:{{$display2}}"><div class="ranking-number-gray">
            @if($dataList->reuters)
            #{{$dataList->reuters}}
            @else
            N/A
            @endif
            </div></div>
            <div class="small-2 columns endcol" style="padding-top:12px;display:{{$display2}}"><div class="ranking-number-gray">
            @if($dataList->shanghai_academic)
            #{{$dataList->shanghai_academic}}
            @else
            N/A
            @endif
            </div></div>
        </div><?php */?>
            @endforeach            
        <div class="row">
        	<div class="small-12 columns" text-align="center" style="text-align:center;padding-top:15px;">{{$RankingData->appends($qstring)->links() }}</div>
        </div>
        @else
        <div class="row">
        	<div class="small-12 columns" text-align="center">No Record Found.</div>
        </div>
        @endif
    <br /><br /><br />
    </div>    
    </div>
    <br />
    <script language="javascript">
        function RankingSort(sortCol)
        {   
            var page='<?=$pageNo?>';
            var order='<?=$order?>';
            var sorting='<?=$sort?>';
            if(sorting==sortCol)
            {
                if(order=="asc")
                order="desc";
                else
                order="asc";
            }
            else
            {
                order="asc";    
            }
            window.location="/ranking/listing?page="+page+"&sort="+sortCol+"&order="+order+"{{$ReqeustStr}}";
        }

        function CompareSchools(){
            var ChkElem=document.getElementsByName('ranking_compare[]');
            var flag=0;
            var svalue=1;
            var strparam="";

            for(var i=0;i<ChkElem.length;i++){
                if(ChkElem[i].checked==true){
                    //alert(ChkElem[i].value);
                    flag++;
                    strparam += ChkElem[i].value+",";
                    svalue++;
                }
            }
            
            if(flag<1){
                alert("Please select at least one school to compare");
                return false;
            }else{
                window.location="/comparison?UrlSlugs="+strparam;
            }
        }
</script>
@stop

