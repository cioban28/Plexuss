    @extends('products.master') @section('content') <!-- product 1 -->
    
   
    <div class="productsHeader">
                Products and Services
    </div>
    
    <div class="products-nav-container">
        <div class="nav-list-container">
           <ul>
            <li id="audience_btn" class="top-btn">
                <button class="top-btn">
                    <div id="audience_nav_btn" class="products-nav-btn topnav-active-state"><img src="/images/products/trans.png"/></div>
                    <div class="product-nav-title">Audience</div>
                </button>
            </li>
            <li id="recruitment_btn" class="top-btn">
                <button class="top-btn">
                    <div id="recruitment_nav_btn" class="products-nav-btn topnav-default-state"><img src="/images/products/trans.png"/></div>
                    <div class="product-nav-title">Recruitment</div>
                </button>
            </li>
            <li id="webinar_btn" class="top-btn">
                <button class="top-btn">
                    <div id="webinar_nav_btn"  class="products-nav-btn topnav-default-state"><img src="/images/products/trans.png"/></div>
                    <div class="product-nav-title">Webinars</div>
                </button>
            </li>
            </ul>
        </div>
    </div>




<div class="main-cont">

    <!--/////////////////// audience section /////////////////////-->
    <div id="audience" class="sec-container">
        <div class="small-6 centered row products-top-container">
            <div class="large-3 pic-col columns"><img  id="audience_big_pic" class="big-icon" src="/images/products/audience-pic.png" /></div>
            <div class="large-8 sec-content columns">
                <h3>Audience</h3>
                <p  class="sec-content-p">Whether your institution is interested in reaching out to students for a specific major, those with high academic scores, or simply looking to diversify &mdash; we have all the information you need to get to know our users!</p>
                <button class="download-btn" onclick=
                "window.open('https://s3-us-west-2.amazonaws.com/asset.plexuss.com/product_page/Media+Kit+Infographic+v10.pdf')">
                Download</button>
            </div>
        </div>  
    </div>
    


    <!--//////////////// recruitment section /////////////////////-->
    <div id="recruitment" class="sec-container">
        <div class="small-6 centered row products-top-container">
            <div class="large-3 pic-col columns"><img  id="recruitment_big_pic" class="big-icon" src="/images/products/recruitment-pic.png" /></div>
            <div class="large-8 recruitment-sec-content columns">
                <h3>Recruitment</h3>
                <p  class="recruitment-sec-content-p">Make connections with college-bound students all over the world.  Discover how Plexuss can contribute to your student recruitment efforts.</p>
                <button class="download-btn" onclick=
                "window.open('https://s3-us-west-2.amazonaws.com/asset.plexuss.com/product_page/plexuss-collateral.pdf')">
                Download</button>
            </div>
        </div>  
    </div>


    <!--/////////////////// webinar section /////////////////////-->
    <div id="webinar" class="sec-container">


        <div class="carousel-container">
        
            
            <h2 class="webinar-mainHeader"> Webinars </h2>
        

            <!-- for hen there are more webinars -->
            <!--div class="arrow-col f-left">
                <img class="arrows arrow-left" src="/images/products/trans.png" />
            </div-->


            <div class="webinar-main f-left">
                <a href="https://vimeo.com/180965385" class="f-left webinar-mov-link" target="blank"> 
                <div class="webinar-container">
                    <img class="webinar-img" src="/images/products/way-to-diversify.gif" />
                    <p class="webinar-caption">Ways to Diversify Your Student Population</p>
                </div>
                </a>

                <a href="https://vimeo.com/180232957" class="f-left webinar-mov-link" target="blank">
                <div class="webinar-container">
                    <img class="webinar-img" src="/images/products/overcoming-budget.gif" />
                    <p class="webinar-caption">Overcoming Your Budget Constraints</p>
                </div>
                </a>
            </div>


            <!-- for hen there are more webinars -->
            <!--div class="arrow-col f-left">
                <img class="arrows arrow-right" src="/images/products/trans.png" />
            </div-->


        </div>
    </div>

    @stop
