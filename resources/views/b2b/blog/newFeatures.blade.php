<?php 
	// dd($data);
?>

<!-- ////////// subscribe section //////////// -->
<div class="mt10"></div>
@include('b2b.blog.blogSubscribe')


<!-- ///////////////// new features content container ////////////// -->
<div class="newf-container clearfix" data-page="1">

		<!--////// month picker - shows last 12 months (including current month) ////// -->
		<?php 
			// $month = date('n');
			// $start = $month % 12; //twelve months
			// $i = $start;

			// $MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

		?>
		<?php
			 // <div class="newf-article-wrapper clearfix">	
			//	<div class="newf-lcol"></div>
			//	<div class="newf-rcol">
			//		<div class="month-slide-cont">
			//			@for($j = 0; $j < 12; $j++, $i= ($i+1) % 12)
							
			//				<span class="newf-month-lnk  @if($month-1 == $i) active @endif">{{  strtoupper($MONTHS[$i])   }}</span>
			//				
			//			@endfor	
						
			//		</div>
			//	</div>
			//</div>
		?>
		<!-- // end month picker ////-->	


		<div class="newf-articles-container">
			@include('b2b.blog.newFeaturesArticle')
		</div>

		<!-- ///////////////// loader //////////// -->
		<div class="newf-article-wrapper clearfix">
			<div class="newf-lcol"></div>
			<div class="newf-rcol">
				
			    <div id="loadmoreajaxloader">
			    	<center>
					    <div  class="loadBox loadingbox1"></div>
					    <div  class="loadBox loadingbox2"></div>
					    <div  class="loadBox loadingbox3"></div>
				    </center>
			    </div>
		    </div>
	    </div>



	</div>

</div>




<!--


else can do this :


1. ajax to get months that have articles within last year
 1a. also get this months articles
2. populate navigation with
	2a. and this months articles -> with infinite scroll



