<html class="no-js" lang="en">
	<head>
		@include('private.headers.header')
	</head>
	<body id="{{$currentPage}}">
		@include('private.includes.topnav')
		<div class="row" style="position:relative">
        <div class='row mobile-news-row show-for-small-only'  id="DropDownMenuNews">
            <div class='column'>
                <ul class="side_nav">
                    <li class="active"><a href="javascript:void(0)"><label>FEATURED</label></a></li>
                    <li><a href="javascript:void(0)"><label>COLLEGE NEWS</label></a></li>
                    <li><a href="javascript:void(0)"><label>PAYING FOR COLLEGE</label></a></li>
                    <li><a href="javascript:void(0)"><label>LIFE AFTER COLLEGE</label></a></li>
                </ul>
            </div>
        </div>
        <div class="custom-row">
			<div class="custom-9"><!-- Left Side Part -->
            <div class="news-header"> 
                <div class="new-header-top hidden-display">
                    <p class="new-head-title"><span>News Listing </span>  <p style="text-align:right"><a href="/addnews">Add News</a></p></p>
                </div>
                <div class="new-header-bottom hidden-display">
                    <ul class="new-filter-nav">
                        <li class="active"><a href="javascript:void(0)">FEATURED</a></li>
                        <li><a href="javascript:void(0)">COLLEGE NEWS</a></li>
                        <li><a href="javascript:void(0)">PAYING FOR COLLEGE</a></li>
                        <li><a href="javascript:void(0)">LIFE AFTER COLLEGE</a></li>
                        <li><a href="javascript:void(0)">|</a></li>
                        <li class="active"><a href="javascript:void(0)">ALL</a></li>
                        <li><a href="javascript:void(0)">NEWEST</a></li>
                        <li><a href="javascript:void(0)">OLDEST</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="clearfix"></div>
            
   
            
                
            <div class="block-1-3 text-center">
				
                @if($newsdata!='')
                @foreach($newsdata as $news)  
                              
                <div class="block-inner theme-green full-640">
                    <div class="block-content-wrapper">
                        <div class="large-12 small-4 column">
                            <div class="bc-image"><img src="images/news_images/{{ $news->news_image_sm }}" title="Image" alt="Image" /><div class="category-badge">                            
                                @if($news->news_category=='1')  HEALTH
                                @elseif($news->news_category=='2')  Famous Alumns
                                @elseif($news->news_category=='3') Career
                                @elseif($news->news_category=='4') Ranking
                                @elseif($news->news_category=='5')  Fashion'
                                @else($news->news_category=='6')   Technology
                                @endif
                            </div></div>
                        </div>
                        <div class="large-12 small-7 column">
                            <p class="bc-heading">{{ $news->news_title }}</p>
                            <p class="bc-time-author hidden-display">by {{ $fname.' '.$lname}}  |  1 hour ago</p>
                            <p class="bc-description hidden-display">{{ substr($news->news_desc,0,50)}}</p>
                            <!--<p class="bc-see-full hidden-display"><a href="/news/101">See full article</a></p>-->
                        </div>
                    </div>	 
                </div>              	
                @endforeach
                @endif
               
               
                
                
            </div>
           
        </div>
			<div class="custom-3"><!-- Right Side Part -->				
				<div class="large-12 columns side-bar-news page-right-side-bar side-bar-1 radius-4"></div>
			</div>
		</div>
        </div>
		@include('private.footers.footer')
	</body>
</html>
