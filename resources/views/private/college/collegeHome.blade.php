<?php
    // dd($currentPage);
?>

<!doctype html>
<html class="no-js" lang="en">
    <head>
        @include('private.headers.header')
    </head>
    <body id="{{$currentPage}}">
        @include('private.includes.topnav')


        <div class="content-wrapper">
            <div class="row collapse fullWidth  college-home-c-wrapper">



            	<!-- left Panel -->
                <div class="hide-for-small-only medium-4 large-3 columns side-bar-1" id="filter-search-div">
                    @include('private.includes.searchLeft')
                </div>

                <!-- Right Side Part -->
                <div id='college-home-content' class="small-12 medium-8 large-9 columns  ">
                    <div class="row">
                        <div class='column small-12'>


                            @include('private.college.collegeNav')


                            <div class="row bg-white">
                                <div class="small-12 use-college-page-title">
                                    <p class="whyCollegePages">Find schools all over the world</p>
                                </div>

                            <div class="search-flags-container">
                              <div id="vmap"></div>
                              </div>
                            </div>
<!--
                            <div class="row excess_container_cmd text-center">
                                <a href="javascript:void" class="btn-more btn-more-yes" onClick="$('.excess_container, .btn-more').toggle();">Show more</a>
                                <a href="javascript:void" class="btn-more btn-more-no" onClick="$('.excess_container, .btn-more').toggle();">Show less</a>
                            </div>
-->
                        </div>
                    </div>
                    <!-- <div class="row show-for-small-only collapse mobilecollegeheader small-text-center">
                        <div class='small-12 column'>
                            <div class="row ">
                                <div class="small-12 column use-college-page-title fs25">
                                    College Pages<br>
                                    <span class=" fs14">Search on Plexuss for colleges</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='row show-for-small-only '>
                        <div class='column small-12'>
                            <div class="row">
                                <div class=" small-12 columns bg-blue">
                                    {{ Form::open(array('action' => 'CollegeController@index', 'data-abide' , 'id'=>'mobilesearch')) }}
                                    <div class="the-search-bar">
                                        <div class='row'>
                                            <div class="small-11 column ui-front collegeInfoAutoComplete1Box">
                                                {{ Form::text('collegeInfoAutoComplete1', null, array('id' => 'collegeInfoAutoComplete1', 'placeholder' => 'Search Colleges..','class'=>'search_txt')) }}
                                                {{ Form::hidden('CollegePickedId1', null , array('id'=>'CollegePickedId1')) }}
                                            </div>
                                            <div class='small-1 column small-search-icon-blue'>
                                                {{-- Form::button('',array('class'=>'search-btn')) --}}
    											<div style='display:inline-block;' class='smallsearchbutton1 search-btn'></div>
                                            </div>
                                        </div>
                                        <!--
                                        <div class='row'>
                                            <div class="small-12 column txt-right clr-fff txt-deco-under fs16 cursor" id="advansed-search-mobile">Advanced Search</div>
                                        </div>
                                        --
                                    </div>
                                    {{ Form::close()}}
                                </div>
                            </div>
                        </div>

                    </div> -->
                    <!--******************************************************** only In Mobile Section *********************************************-->

                    <!-- **************** -->
                    <!-- **************** -->
                    <!-- **************** -->

                    @include ('private.college.collegeMajors_onhome')
                    @include ('private.college._oldcollegeContent')
                    <!-- **************** -->
                    <!-- **************** -->
                    <!-- **************** -->






                </div>
            </div>



        </div>


        @include('private.footers.footer')

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
        <script type="text/javascript" src="/js/worldMap/jquery.vmap.min.js"></script>
		<script type="text/javascript" src="/js/worldMap/jquery.vmap.world.js" charset="utf-8"></script>
		<script type="text/javascript" src="/js/worldMap/jquery.vmap.usa.js" charset="utf-8"></script>
        <script type="text/javascript">
            $("#smallsearchform").validate({
                rules: {
                    srch_college: {
                        required: true
                    }
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });

            $(document).ready(function(e){

				// Blue search box autocomplete
				$("#collegeInfoAutoComplete1").autocomplete({
					source:"getBattleAutocomplete?type=college",
					minLength: 1,
					create: function () {
						// Inserts image into autocomplete list
						$(this).data( "ui-autocomplete" )._renderItem = function( ul, item ) {

						 var inner_html = '';

							var inner_html = '';
							inner_html +=  '<a><div class="list_item_container">';
							inner_html +=  '<div class="image"><img src="'+ item.image + '"></div><div class="title">' + item.label + '</div>';
							inner_html +=  '<div class="description">' + item.state + '</div>';
							inner_html +=  '</div></a>';

						 return $( "<li></li>" )
						.data( "item.autocomplete", item )
						.append(inner_html)
						.appendTo( ul );
						};
					},
					change: function(){
						var form = $(this);
						var val = form.val();
						var data = form.data('value');

						// Clears the search field if user has not made a selection from the autocomplete dropdown
						if( val != data ){
							form.val('');
							form.data( 'slug', '' );
						}
					},
					select: function(event, ui) {
						// Sets the slug, used by search button click
						$(this).data( 'slug', ui.item.slug );
						// sets value, used to force user to select from autocomplete
						$(this).data( 'value', ui.item.value );
					}
				});

				// JS for search button, on click sends user to college page
				$('.smallsearchbutton1').click(function(event) {
					var slug = $('#collegeInfoAutoComplete1').data('slug');
					if ( slug ) {
						window.location.href =("/college/" + slug );
					}
				});
                //enter key used to perform same function as when submit button is clicked
                $('#collegeInfoAutoComplete1').bind("keyup keypress", function(e) {
                    var code = e.keyCode || e.which;
                    var autoCompl_slug = $('#collegeInfoAutoComplete1').data('slug');
                    var autoCompl_value = $('#collegeInfoAutoComplete1').data('value');

                    if(code == 13) {
                        if( autoCompl_value == undefined || autoCompl_slug == undefined ){
                            //prevent form submission - slug and value must be set
                            e.preventDefault();
                            return false;
                        }else{
                            //allow form submission
                            $('.smallsearchbutton1').click();
                        }
                    }//end of if key pressed logic
                });
                //auto complete for the battle page.
                //collegeAutocomplete('collegeAutoComplete1','collegeAutoCompleteId1');
                //collegeAutocomplete('collegeAutoComplete2','collegeAutoCompleteId2');
                //collegeAutocomplete('collegeAutoComplete3','collegeAutoCompleteId3');

				collegeAutocomplete('collegeAutoComplete1','collegeAutoCompleteId1');
                collegeAutocomplete('collegeAutoComplete2','collegeAutoCompleteId2');
                collegeAutocomplete('collegeAutoComplete3','collegeAutoCompleteId3');

				$('#vmap').vectorMap({
					map: 'world_en',
					backgroundColor: '#ffffff',
					color: '#000000',
					hoverColor: '#26B24B',
					selectedColor: '#26B24B',
					enableZoom: false,
					showTooltip: true,
					normalizeFunction: 'polynomial',
					onResize: function (element, width, height) {
						console.log('Map Size: ' +  width + 'x' +  height);
					},
					onRegionClick: function(event, code, region){
						if(code == "us"){
							$('#vmap').replaceWith("<div id='vmap'><div class='world-map-back'></span><span class='majors-back-arrow'>‹</span> Back</div></div>");
							var $map = $('#vmap');
							$map.vectorMap({
								map: 'usa_en',
								backgroundColor: '#ffffff',
								color: '#000000',
								hoverColor: '#26B24B',
								selectedColor: '#26B24B',
								enableZoom: false,
								showTooltip: true,
								onResize: function (element, width, height) {
									console.log('Map Size: ' +  width + 'x' +  height);
								},
								onRegionClick: function(event, code, region){
									window.location = "college/state/colleges-in-"+region.toLowerCase();
								},
							});
						} else {
							window.location = "/search?type=college&term=&country="+code;
						}
					},

					onLabelShow: function(event, label, code){
						if (code == 'us'){
							event.preventDefault();
						}
					},
				});

				$(document).on('click', '.world-map-back', function() {
					$('#vmap').replaceWith("<div id='vmap'></div>");
					var $map = $('#vmap');
					$map.vectorMap({
						map: 'world_en',
						backgroundColor: '#ffffff',
						color: '#000000',
						hoverColor: '#26B24B',
						selectedColor: '#26B24B',
						enableZoom: false,
						showTooltip: true,
						normalizeFunction: 'polynomial',
						onResize: function (element, width, height) {
							console.log('Map Size: ' +  width + 'x' +  height);
						},
						onRegionClick: function(event, code, region){
							if(code == "us"){
								$('#vmap').replaceWith("<div id='vmap'><div class='world-map-back'></span><span class='majors-back-arrow'>‹</span> Back</div></div>");
								var $map = $('#vmap');
								$map.vectorMap({
									map: 'usa_en',
									backgroundColor: '#ffffff',
									color: '#000000',
									hoverColor: '#26B24B',
									selectedColor: '#26B24B',
									enableZoom: false,
									showTooltip: true,
									onResize: function (element, width, height) {
										console.log('Map Size: ' +  width + 'x' +  height);
									},
									onRegionClick: function(event, code, region){
										window.location = "college/state/colleges-in-"+region.toLowerCase();
									}
								});
							} else {
								window.location = "/search?type=college&term=&country="+code;
							}
						},
						onLabelShow: function(event, label, code){
							if (code == 'us'){
								event.preventDefault();
							}
						},
					});
				});

    		});


            $('#advansed-search-mobile').click(function(){
                $( "#filter-search-div").toggle();
            })

            function show_profile_block(){
                $('.mobile-profile-row').toggle();
            }

            function toggle_menu_button(){
                $('#menu-toggler').trigger('click');
            }


            //reload zurb items.
            $(document).foundation();
            $("#collegeInfoAutoComplete").val('');

            $(function() {

                $("#collegeInfoAutoComplete").autocomplete({
                    source:"getAutoCompleteData?zipcode=" + '95376' + "&type=college",
                    minLength: 1,
                    select: function(event, ui) {
                        $(this).data('hsname', ui.item.label);
                        $('.smallsearchbutton').data('slug',ui.item.slug );
                    }
                });

                $("#collegeInfoAutoComplete").change(function() {
                    var _this = $(this);
                    if (_this.val() !== _this.data('hsname')) {
                        _this.val('');
                        $('.smallsearchbutton').removeData('slug');
                    }
                });

                $('.smallsearchbutton').click(function(event) {
                    if ($(this).data('slug')) {
                        var slug = $(this).data('slug');
                        window.location.href =("/college/"+ slug );
                    };
                });
                $('.stopsubmit').bind("keyup keypress", function(e) {
                    var code = e.keyCode || e.which;
                    if (code  == 13) {
                        e.preventDefault();
                        return false;
                    }
                });
            });

			// Range Slide On Kayak Advanced Search
			RangeSliderMax('slider-range-1','tuition_range','tuition_max_val', 0);
			RangeSlider('slider-range-2','enrollment_range','enrollment_min_val','enrollment_max_val','0','250000');
			RangeSlider('slider-range-3','applicants_range','applicants_min_val','applicants_max_val','0','100');
			RangeSlider('slider-range-0','miles_range','miles_range_min_val','miles_range_max_val','0','250');
			// SRange Slide On Kayak Advanced Search

			// Select box data load in Kayak Advanced Search
			//AjaxSelectBox('city','getSelectBoxVal','city-select-box','');
			AjaxSelectBox('country','getSelectBoxVal','country-select-box','');
			AjaxSelectBox('state','getSelectBoxVal','state-select-box','');
			AjaxSelectBox('locale','getSelectBoxVal','locale-select-box','');
			AjaxSelectBox('religious_affiliation','getSelectBoxVal','religious-select-box','');
			// Select box data load in Kayak Advanced Search

             $("#owl-compare").owlCarousel({
                items :3,
                itemsDesktop : [1199,3],
                itemsDesktopSmall : [979,3],
                itemsMobile : [479,3],
                itemsCustom : [320,3],
                navigation : true, // Show next and prev buttons
                slideSpeed : 300,
                paginationSpeed : 400,
                singleItem:false
                });

                $(".msg-carousel").owlCarousel({
                items :1,
                navigation : true, // Show next and prev buttons
            	slideSpeed : 300,
            	pagination  :   false,
            	paginationSpeed : 400,
            	navigationText : ["<li class='nav-arrow navleft-arrow' data-index='0' id='conference-prev'></li>","<li class='nav-arrow navright-arrow' data-index='2' id='conference-next'></li>"],
            	singleItem:true,
            	rewindNav :false,
            	mouseDrag : true,
            	touchDrag : true
                });

				function setResizeBox() {
				$('#container-box').masonry({
					itemSelector: '.box-div'
				});
				};

			function expandDiv(expandID)
			{
			$('#'+expandID).slideToggle(500, function()
			{
				$('#'+expandID).parent().siblings().find('.expand-toggle-list').toggleClass("run");
                $('#'+expandID).parent().siblings().find('.expand-collapse-img').toggleClass('expand-collapse-img-toggle');
				setResizeBox();
				});
			}
			$(".navright-arrow,.navleft-arrow").click(function(){
			$('.expandcollapse').hide();
			$('.expand-toggle-list').removeClass("run");

			});

            $("#owl-demo").owlCarousel({
                itemsCustom: [
                    [0, 5],
                    [641, 10]
                ],
                navigationText: false,
                navigation: true,
                pagination: false
            });
			
			function CheckisUs(country_name){
				if(country_name !="US"){
					$("#state_div").html('<input name="state" type="text" value="" placeholder="State Name" id="state-select-box" />');
					$("#city_div").html('<input name="city" type="text" value="" placeholder="City Name" id="city-select-box" />');
				}else{
					$("#state_div").html('<select class="styled-select" id="state-select-box" name="state"><option value="0">No preference</option></select>');
					$("#state-select-box").attr('onchange','AjaxSelectBox("city","getSelectBoxVal","city-select-box","",this.value);');
					$("#city_div").html('<select class="styled-select" id="city-select-box" name="city"><option value="">No preference</option></select>');
					AjaxSelectBox('state','getSelectBoxVal','state-select-box','');
				}
			}
        </script>
    </body>
</html>
