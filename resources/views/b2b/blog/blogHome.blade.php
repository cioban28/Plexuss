<?php

	// dd($data);
?>

			<!-- ////// featured news /////// -->
			@include('b2b.blog.featured')

			<!-- ////////// subscribe section //////////// -->
			@include('b2b.blog.blogSubscribe')


			<!--///////// blog boxes container //////////-->
			<div id="container-box" class="blog-box-container js-masonry row"  data-masonry-options='{ "itemSelector": ".newsitem" }' data-page="1"  data-pageTotal="data['total']">

				<!-- for each blog post -->
				@include('b2b.blog.blogBox')
		       

			</div><!-- end masonry container -->



			<!-- ///////////////// loader //////////// -->
		    <div id="loadmoreajaxloader">
		    	<center>
				    <div  class="loadBox loadingbox1"></div>
				    <div  class="loadBox loadingbox2"></div>
				    <div  class="loadBox loadingbox3"></div>
			    </center>
		    </div>
		