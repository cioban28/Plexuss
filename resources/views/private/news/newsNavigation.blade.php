 <?php 
 // dd($data);
 ?>
 <div class='row'>
    <div class='column small-12 news-nav-container'>
        <div class="news-header row collapse">
            <div class="new-header-bottom hidden-display column small-12 medium-8">
                <ul class="new-filter-nav">
                    <li class="active"><a href="/news/">All</a></li>
                    <!-- college Essays tab -->
                    <li id="college-essays-li" class="news-drpdwn-essays"><a class="essay-nav" href="/news/catalog/{{'college-essays'}}/">STUDENT ESSAYS</a> 
                    </li>
                    <!-- Articles tab -->
                    <li id="college-essays-li" class="news-drpdwn-articles"><span>ARTICLES <span class="navigation-arrow-down1">&nbsp;</span></span> 
                        <ul class="articles-menu">   
                            <li id="collge-list-li" class="news-drpdwn-box1 "><a  class="collegenews-nav" href="/news/catalog/{{'college-news'}}/">COLLEGE NEWS <span class="navigation-arrow-green">&nbsp;</span></a> 
                                <ul class="college-menu news-dropdwn-college" id="collge-list-div">
                                	@if(isset( $news_scat_data) && $news_scat_data!='')
                                    @foreach($news_scat_data as $news_scat)
                                         <li><a href="/news/subcategory/{{$news_scat['slug']}}/">{{ $news_scat['name']}}</a></li>
                                    @endforeach
                                    @endif
                                </ul>
                            </li>
                            <li id="mylist-list-li" class="news-drpdwn-box2"><a class="paying-nav" href="/news/catalog/{{'paying-for-college'}}/">PAYING FOR COLLEGE <span class="navigation-arrow-gold">&nbsp;</span></a> 
                                <ul class="college-menu  news-dropdwn-paying" id="mylist-list-div">
                                    @if(isset($college_after_data) && $college_scat_data!='')
                                    @foreach($college_scat_data as $college_subcat)
                                        <li><a href="/news/subcategory/{{$college_subcat['slug']}}/">{{ $college_subcat['name']}}</a></li>
                                    @endforeach
                                    @endif
                                </ul>
                            </li>
                            <li id="aftercoll-cat-li" class="news-drpdwn-box3"><a class="lifeafter-nav" href="/news/catalog/{{'life-after-college'}}/">LIFE AFTER COLLEGE <span class="navigation-arrow-cyan">&nbsp;</span></a> 
                                <ul class="college-menu  news-dropdwn-lifeafter" id="after-college-div">
                                    @if(isset($college_after_data) && $college_after_data!='')
                                    @foreach($college_after_data as $collegeafter_subcat)
                                        <li><a href="/news/subcategory/{{$collegeafter_subcat['slug']}}/">{{ $collegeafter_subcat['name']}}</a></li>
                                    @endforeach
                                    @endif
                                </ul>
                            </li>
                        </ul>
                     </li>  
                    <!-- Testimonials tab -->
                   <!--  <li id="college-essays-li" class="news-drpdwn-testimonials"><a class="testimonial-nav" href="/news/catalog/{{'testimonials'}}/">TESTIMONIALS <span class="pink-heart">
                    &hearts;</span></a> 
                    </li> -->
                </ul>
            </div>
            <div class="column small-12 medium-4">
                <div class="clearfix news-header-part2">
                @if(isset($OrderUrl1) && isset($OrderUrl2))
                    <div class="left">
                        <a class="newest-a @if($order == 'desc') current @endif" href="{{$OrderUrl1}}">NEWEST</a> 
                        <b>|</b> 
                        <a class="oldest-a @if($order == 'asc') current @endif" href="{{$OrderUrl2}}">OLDEST</a>
                    </div>
                    <div class="right university-search-tab hide-for-small-only text-right">
                        <span class="magnifier-icon"><span class="magnifier"></span></span>
                        <a href="" class="search-articles-btn mr10">UNIVERSITIES</a>
                    </div>
                @endif
                </div>
            </div>
        </div>
       <!-- search container -->
        <div class='show-for-medium-up search-box'>
            <div class="search-articles-container">
                <!-- search bar -->
                <div class="row collapse search-bar">
                    <div class="small-10 column">
                        {{Form::text('search_articles', null, array('class' => '', 'placeholder' => 'Search by University or keyword'))}}
                    </div>
                    <div class="small-2 column submit-container" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/Search.png, (default)]">
                        <a href="" class="submit-search postfix"></a>
                    </div>
                </div>

                <div class="row collapse col-headers">
                    <div class="column small-8"><h5><b>Articles & Videos</b></h5></div>
                    <div class="column small-4"><h5 class="text-center"><b>My Schools</b></h5></div>
                </div>
                <!-- results -->
                <div class="result-and-school-container"> 
                    <div class="row collapse">
                        <!-- results -->
                        <div class="column small-8 results-container"> 
                            <div class="results">
                                <!-- results injected here -->
                                <div class="intro-msg">
                                    <b>Search something to get started...</b>
                                </div>
                                <div>No results</div>
                            </div>
                            <div class="loader hide">
                                <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/loading.gif" alt="search loader">
                            </div>
                        </div>
                        <!-- recruited schools -->
                        <div class="column small-4 school-container"> 
                            <ul>
                                @if( isset($signed_in) && $signed_in == 1 )
                                    @if( isset($college_recruits) && !empty($college_recruits) )
                                        @foreach($college_recruits as $college)
                                            <li><a href="" class="college-recruit">{{$college['name']}}</a></li>
                                        @endforeach
                                    @else
                                        <li class="text-center">No schools in your list</li>
                                    @endif
                                @else
                                    <li class="text-center">
                                        <a href="/signin?redirect=news">Signin</a> to see schools
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div><!-- end results and school cont -->         
            </div>
        </div><!-- end search container -->
    </div><!-- end inner nav container -->
</div>
<!-- end desktop menu -->


