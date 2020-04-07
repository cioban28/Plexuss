 <div id="container-box" class="js-masonry row">
                        <!-- compare box -->
                    	<div class="box-div column small-12 large-6" id="compare-box-div">
                            <div id="comparebox_content_div">
                                <div class="header-banner" style="background-color:#04a6ae">Compare Colleges</div>
                                <div class="banner-content-div" style="background-color:#03747a;">
                                    <div class="college-compare-outer">
                                        <div class="college-compare-head1" style="text-align:center;"><span style="font-weight:bold;">BATTLE</span> SCHOOLS</div>
                                        <div class="college-compare-head2 small-text-center">COMPARE THE TOP STATS OF ANY SCHOOLS</div>
                                        <div class="college-compare-div-first ui-front"><input id="collegeAutoComplete1" placeholder="Start typing a school name..." class="search-text-box" name="collegeAutoComplete1" type="text"><input id="collegeAutoCompleteId1" name="collegeAutoCompleteId1" type="hidden">
                                            <div style="position:absolute;font-size:20px;color:#797979;font-weight:bold;top:23px;right:15px;">1</div>
                                        </div>
                                        <div class="college-compare-div-other college-compare-div-second ui-front"><input id="collegeAutoComplete2" placeholder="Start typing a school name..." class="search-text-box" name="collegeAutoComplete2" type="text"><input id="collegeAutoCompleteId2" name="collegeAutoCompleteId2" type="hidden">
                                            <div class="college-compare-right-other">2</div>
                                        </div>
                                        <div class="college-compare-div-other college-compare-div-third ui-front"><input id="collegeAutoComplete3" placeholder="Start typing a school name..." class="search-text-box" name="collegeAutoComplete3" type="text"><input id="collegeAutoCompleteId3" name="collegeAutoCompleteId3" type="hidden">
                                            <div class="college-compare-right-other">3</div>
                                        </div>
                                         <div style="text-align:center;" class="footer-banner college-compare-comparebox">
                                            <div onClick="redirectBattle('collegeAutoCompleteId1','collegeAutoCompleteId2','collegeAutoCompleteId3');">
                                                <img alt='batlleimage' src="/images/colleges/battle.png"> &nbsp; <span class="battlefont f-normal">Battle !</span>
                                            </div>
                                        </div>
                                        <div style="height:26px;">&nbsp;</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- show this video here on mobile only -->            
                        <div class="small-12 columns small-text-center show-for-small-only collegeHome-mobileOnly-vid">
                            <img style='cursor: pointer;' data-reveal-id="college-home-ranking-video" src="/images/video-images/college-video.png" alt="Plexuss College Video">
                        </div>

                        <div class="box-div column small-12 large-6" id="intersting-div1">
                            <div class="header-banner" style="background-color:#d93600">Interesting Lists</div>
                            <div class="banner-content-div" style="background-color:transparent;">
                                <div class="owl-carousel msg-carousel">
                                    @foreach($lists['interesting'] as $key=>$interestingList)
                                        <div style="background-color:#040404;">
                                        <div class="item text-center text-white p5 fs16 bold-font">{{$key}}</div>

                                        <div class="rank-div-header-box-interest row">
      
                                          <div class="column small-3 text-center coll-interestingList-header-left">
                                            PLEXUSS&nbsp;<br>RANK
                                          </div>
                                          <div class="column small-9 text-center coll-interestingList-header-right">
                                            SCHOOL NAME
                                          </div>
                                                                               
                                        </div>
                                        
                                        <div class="row-data" style="min-height:270px;">
                                        @foreach($lists['interesting'][$key] as $key1=>$datalisting)
                                            @if($key1==4)
                                                <div class="expandcollapse" style="display:none;" id="expand_list_interest_{{$datalisting->list_id}}">
                                            @endif
                                            <!--<div>
                                            <ul class="ul-d-inline">
                                                <li class="box_image-no mt10 ml10">#{{$datalisting->plexuss_rating}}</li>
                                                <li class="pl25" style="width:80%">
                                                <span class="battlefont fs14"><a href="/college/{{$datalisting->slug}}" style="color:#fff;">{{$datalisting->school_name}}</a></span><br>
                                                <span class="battlefont fs14 f-normal ">{{$datalisting->city}} , {{$datalisting->long_state}}</span>
                                                </li>
                                            </ul>
                                            </div>-->

                                            <div class="row">
                                                <div class="column small-12 mt10">

                                                    <div class="row">
                                                        <div class="column small-3 small-text-center mt10">
                                                            <span class="box_image-no">#{{$datalisting->plexuss_rating or 'N/A'}}</span>
                                                        </div>

                                                        <div class="column small-9">
                                                            <div class="row">
                                                                <div class="column small-12">
                                                                    <a href="/college/{{$datalisting->slug}}" class="fs14 battlefont">
                                                                        {{$datalisting->school_name}}
                                                                    </a>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="column small-12 battlefont fs14 f-normal">
                                                                    {{$datalisting->city}} , {{$datalisting->long_state}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>  

                                         @endforeach
                                        @if($key1>=4)
                                        </div>
                                        @endif
                                        </div>
                                        <div class="footer-banner" style="background-color:#d93600;cursor:pointer;" onClick="expandDiv('expand_list_interest_<?php echo $datalisting->list_id ?>');">
                                            <h6 class="battlefont fs14 txt-center expand-toggle-list"></h6>                            
                                            <img alt='expand image' src="/images/colleges/expand.png" class="expand-collapse-img">
                                        </div>
                                     </div>            
                                    @endforeach
                                </div>             
                                                        
                            </div>
                        </div>

                        <div class="box-div column small-12 large-6" id="conference-div">
                            <div class="header-banner" style="background-color:#26b24b">Schools By Conference</div>
                            <div class="banner-content-div" style="background-color:transparent;">
                                <div class="owl-carousel msg-carousel">
                                    @foreach($lists['conference'] as $key=>$conferenceList)
                                        <div style="background-color:#040404;">
                                        <div class="item text-center text-white p5 fs16 bold-font">{{$key}}</div>

                                        <div class="rank-div-header-box-conf row">
                                            <div class="column small-3 text-center coll-interestingList-header-left">
                                                PLEXUSS&nbsp;<br>RANK
                                            </div>
                                            <div class="column small-9 text-center coll-interestingList-header-right">
                                                SCHOOL NAME
                                            </div>
                                        </div>

                                        <div class="row-data" style="min-height:270px;">
                                        @foreach($lists['conference'][$key] as $key1=>$datalisting)
                                            @if($key1==4)
                                                <div class="expandcollapse" style="display:none;" id="expand_list_conference_{{$datalisting->list_id}}">
                                            @endif
                                            <!--<div>
                                            <ul class="ul-d-inline">
                                                <li class="box_image-no mt10 ml10">#{{$datalisting->plexuss_rating}}</li>
                                                <li class="pl25" style="width:80%">
                                                <span class="battlefont fs14"><a href="/college/{{$datalisting->slug}}" style="color:#fff;">{{$datalisting->school_name}}</a></span><br>
                                                <span class="battlefont fs14 f-normal ">{{$datalisting->city}} , {{$datalisting->long_state}}</span>
                                                </li>
                                            </ul>
                                            </div>-->
                                            <div class="row">
                                                <div class="column small-12 mt10">

                                                    <div class="row">
                                                        <div class="column small-text-center small-3 mt10">
                                                            <span class="box_image-no">#{{$datalisting->plexuss_rating or 'N/A'}}</span>
                                                        </div>

                                                        <div class="column small-9">
                                                            <div class="row">
                                                                <div class="column small-12">
                                                                    <a href="/college/{{$datalisting->slug}}" class="battlefont fs14">
                                                                        {{$datalisting->school_name}}
                                                                    </a>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="column small-12 battlefont fs14 f-normal">
                                                                    {{$datalisting->city}} , {{$datalisting->long_state}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>

                                         @endforeach
                                        @if($key1>=4)
                                        </div>
                                        @endif
                                        </div>
                                        <div class="footer-banner" style="background-color:#26b24b;cursor:pointer;" onClick="expandDiv('expand_list_conference_<?php echo $datalisting->list_id?>');">
                                            <h6 class="battlefont fs14 txt-center expand-toggle-list" ></h6>
                                            <img alt='expand image' src="/images/colleges/expand.png" class="expand-collapse-img">
                                        </div>
                                     </div>
                                    @endforeach
                               </div>
                            </div>
                        </div>

                        <!-- directory box -->
                        <div class="box-div column small-12 large-6" id="directory-box-div">
                            <div class='row'>
                                <div class="header-banner column small-12" style="background-color:#006dd9">Directory A-Z</div>
                            </div>
                            <div class="banner-content-div" style="background-color:#040404">
                                <div class="row collapse">

                                    <div class='column small-10 small-centered'>
                                        <div id="owl-demo" class="owl-carousel">
                                            <div class="item" onClick="filterCollege('#')">#</div>
                                            <div class="item" onClick="filterCollege('A')">A</div>
                                            <div class="item" onClick="filterCollege('B')">B</div>
                                            <div class="item" onClick="filterCollege('C')">C</div>
                                            <div class="item" onClick="filterCollege('D')">D</div>
                                            <div class="item" onClick="filterCollege('E')">E</div>
                                            <div class="item" onClick="filterCollege('F')">F</div>
                                            <div class="item" onClick="filterCollege('G')">G</div>
                                            <div class="item" onClick="filterCollege('H')">H</div>
                                            <div class="item" onClick="filterCollege('I')">I</div>
                                            <div class="item" onClick="filterCollege('J')">J</div>
                                            <div class="item" onClick="filterCollege('K')">K</div>
                                            <div class="item" onClick="filterCollege('L')">L</div>
                                            <div class="item" onClick="filterCollege('M')">M</div>
                                            <div class="item" onClick="filterCollege('N')">N</div>
                                            <div class="item" onClick="filterCollege('O')">O</div>
                                            <div class="item" onClick="filterCollege('P')">P</div>
                                            <div class="item" onClick="filterCollege('Q')">Q</div>
                                            <div class="item" onClick="filterCollege('R')">R</div>
                                            <div class="item" onClick="filterCollege('S')">S</div>
                                            <div class="item" onClick="filterCollege('T')">T</div>
                                            <div class="item" onClick="filterCollege('U')">U</div>
                                            <div class="item" onClick="filterCollege('V')">V</div>
                                            <div class="item" onClick="filterCollege('W')">W</div>
                                            <div class="item" onClick="filterCollege('X')">X</div>
                                            <div class="item" onClick="filterCollege('Y')">Y</div>
                                            <div class="item" onClick="filterCollege('Z')">Z</div>
                                        </div>
                                    </div>
                                    <div class="small-12 pt10">
                                        <div class="small-10 columns">
                                            <input type="text" name="search" class="search_txt" id="search_txt" onKeyUp="filterCollege(this.value)"/>
                                        </div>
                                        
                                        <div class="small-2 columns">
                                            <input type="button" class="search-btn" style="border:none" onClick="filterCollege($('#search_txt').val())"/>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="directory scrollbar">
                                        <div style="text-align:center;" id="ajaxloader-div" class="d-none">
                                            <img src="/images/colleges/laying-bricks-loader-green_2.gif" style="height:80px; width:80px;" alt="">
                                        </div>
                                        <div id="letterFilterd" style="line-height:22px;">
                                            @foreach($dirAList as $college)
                                                <?php
                                                    $string=substr($college->school_name,0,20).'..';
                                                    $showstr=str_replace("'","",$string);
                                                ?>
                                                <div class="li-filterdata">
                                                    <a href="/college/{{$college->slug}}">{{ $string }}.. ({{ $college->state }})</a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="footer-banner" style="background-color:#040404;min-height:30px;">&nbsp;</div>
                        </div>

                    </div>