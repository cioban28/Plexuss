@extends('products.masterGeneral')

@section('content')
    <div class="banner">
      <div class="row">
        <div class="banner-caption">
          <h1>Fulfill Your Dream of</h1><h1>Studying in the United States</h1>
        </div>
        <div align="right">
          @if($signed_in == 1)
          <a href="/checkout/premium" class="button premium-btn">Get Plexuss Premium Now!</a>
          @elseif(isset($showSignUp) && $showSignUp == 0)
          <a href="/signin{{$utm_source}}&utm_medium={{$utm_medium}}_get-premium-now{{$utm_campaign}}{{$utm_content}}&redirect=checkout/premium{{$utm_term}}" class="button premium-btn">Get Plexuss Premium Now!</a>
          @else
          <a href="/signup{{$utm_source}}&utm_medium={{$utm_medium}}_get-premium-now{{$utm_campaign}}{{$utm_content}}&redirect=/get_started/9{{$utm_term}}" class="button premium-btn">Get Plexuss Premium Now!</a>
          @endif
        </div>
      </div>
    </div>
    <div class="international-logo">
      <img src="/css/premiumGeneral/images/logo.png">
    </div>
    <div class="one-on-one-block">
      <div class="row">
        <div class="large-12 medium-12 columns fulfill-block">
          <h1 class="ono-on-one-header">Fulfill Your Dream of Studying in the United States</h1>
          <p>
            Offering a world-class education, improved career prospects after graduation, and a potential pathway for immigration - the United States continues to be one of the most popular study abroad destination for international students. Whether you’re interested in computer science, business, medicine, or engineering, we have more than 100 partner universities who can provide you the education that you’re looking for.</p>
            <br/>
            <p class="mob-bg">Hands-on OPT training - flexible degree programs - scholarship opportunities - a welcoming environment - if you’re serious about studying in the United States, Plexuss can help.</p>
        </div>
      </div>
    </div>
    <div class="post-area">
      <div class="row post">
        <h3>Guaranteed I-20 to get your Visa</h3>
        <div class="large-6 medium-6 small-12 columns post-textarea">
          <div class="image-holder mobile-image visa-image">
            <img src="/css/PremiumIndia/images/Guaranteed_I-20_to_get_your_visa.png" class="custom-thumbnail-class" >
            <div class="guarantee-image"><img src="/css/premiumGeneral/images/Group.png"></div>
          </div>
          <div class="mobile-text-p">
            <p>With over 15 years of collective experience helping international students to get into highly reputable US universities, we have the knowledge and expertise to help you attend the right school. Upgrade to Plexuss Premium with no risk - if we are unable to secure you at least one acceptance letter from university after 12 months of signing up to the service, you will receive a full refund.
              </p>
            @if($signed_in == 1)
            <a href="/checkout/premium" class="button custom-btn visa-custom-btn">Guarantee University Acceptance</a>
            @elseif(isset($showSignUp) && $showSignUp == 0)
            <a href="/signin{{$utm_source}}&utm_medium={{$utm_medium}}_guarantee-univ-acceptance{{$utm_campaign}}{{$utm_content}}&redirect=checkout/premium{{$utm_term}}" class="button custom-btn visa-custom-btn">Guarantee University Acceptance</a>
            @else
            <a href="/signup{{$utm_source}}&utm_medium={{$utm_medium}}_guarantee-univ-acceptance{{$utm_campaign}}{{$utm_content}}&redirect=/get_started/9{{$utm_term}}" class="button custom-btn visa-custom-btn">Guarantee University Acceptance</a>
            @endif
          </div>
        </div>
        <div class="large-6 medium-6 small-12 columns">
          <div class="image-holder visa-image">
            <img src="/css/PremiumIndia/images/Guaranteed_I-20_to_get_your_visa.png" class="custom-thumbnail-class" >
            <div class="guarantee-image"><img src="/css/premiumGeneral/images/Group.png"></div>
          </div>
        </div>
      </div>
      <div class="row post">
        <h3>Free College Applications</h3>
        <div class="large-6 medium-6 small-12 columns " >
          <div class="image-holder">
            <img src="/css/PremiumIndia/images/FreeCollegeApplication.png">
          </div>
        </div>
        <div class="large-6 medium-6 small-12 columns post-textarea post-left-text">
          <div class="image-holder mobile-image">
            <img src="/css/PremiumIndia/images/FreeCollegeApplication.png" class="custom-thumbnail-class" >
          </div>
           <div class="mobile-text-p">
            <p>Most application fees range from $50-100 each, but with Plexuss Premium you are given 5 applications to select universities in our network for free, saving you an additional $250.
            </p>
            @if($signed_in == 1)
            <a href="/checkout/premium" class="button custom-btn visa-custom-btn">Save on Application Fees</a>
            @elseif(isset($showSignUp) && $showSignUp == 0)
            <a href="/signin{{$utm_source}}&utm_medium={{$utm_medium}}_save-application-fees{{$utm_campaign}}{{$utm_content}}&redirect=checkout/premium{{$utm_term}}" class="button custom-btn visa-custom-btn">Save on Application Fees</a>
            @else
            <a href="/signup{{$utm_source}}&utm_medium={{$utm_medium}}_save-application-fees{{$utm_campaign}}{{$utm_content}}&redirect=/get_started/9{{$utm_term}}" class="button custom-btn visa-custom-btn">Save on Application Fees</a>
            @endif
          </div>
        </div>
      </div>
      <div class="row post">
        <h3>1-on-1 Support</h3>
        <div class="large-6 medium-6 columns small-12">
          <div class="image-holder">
            <img src="/css/PremiumIndia/images/1-on-1Support.png">
          </div>
        </div>
        <div class="large-6 medium-6 small-12 columns post-textarea post-left-text">
          <div class="image-holder mobile-image">
            <img src="/css/PremiumIndia/images/1-on-1Support.png" class="custom-thumbnail-class" >
          </div>
          <div class="mobile-text-p">
            <p>Receive one-on-one support over the next few months as you prepare for college, including help filling out your applications and understanding what documents you will need to include. These services are often provided by agents at a cost of $1,000 but with Plexuss Premium you get the same support plus the free applications and access to admissions essays for $499 total.
            </p>
            @if($signed_in == 1)
            <a href="/checkout/premium" class="button custom-btn visa-custom-btn support-btn">Get Accepted to a Top US University</a>
            @elseif(isset($showSignUp) && $showSignUp == 0)
            <a href="/signin{{$utm_source}}&utm_medium={{$utm_medium}}_get-accepted-to-a-top-us-uni{{$utm_campaign}}{{$utm_content}}&redirect=checkout/premium{{$utm_term}}" class="button custom-btn visa-custom-btn support-btn">Get Accepted to a Top US University</a>
            @else
            <a href="/signup{{$utm_source}}&utm_medium={{$utm_medium}}_get-accepted-to-a-top-us-uni{{$utm_campaign}}{{$utm_content}}&redirect=/get_started/9{{$utm_term}}" class="button custom-btn visa-custom-btn support-btn">Get Accepted to a Top US University</a>
            @endif
          </div>
        </div>
      </div>
      <div class="row study-abroad-block premium-general-promise">
        <h1>The Plexuss Premium Promise</h1>
        <h3>You Deserve the Best Study Abroad Experience</h3>
        <ul class="premium-promise-list">
          <li>
            <span class="promise-tick"><img src="/css/PremiumIndia/images/checkmark.png"></span>
            <p class="promise-text">Guaranteed 1-20 Form within 12 Months</p>
          </li>
          <li>
            <span class="promise-tick"><img src="/css/PremiumIndia/images/checkmark.png"></span>
            <p class="promise-text">One-on-one University Admission Assistance</p>
          </li>
          <li>
            <span class="promise-tick"><img src="/css/PremiumIndia/images/checkmark.png"></span>
            <p class="promise-text">Access to Admission Essays written by Students Admitted to Elite Universities</p>
          </li>
          <li>
            <span class="promise-tick"><img src="/css/PremiumIndia/images/checkmark.png"></span>
            <p class="promise-text">Expert Support Helping You Choose a School in the USA, UK, or Australia</p>
          </li>
          <li>
            <span class="promise-tick"><img src="/css/PremiumIndia/images/checkmark.png"></span>
            <p class="promise-text"> Five Waived College Application Fees to Select Universities</p>
          </li>
        </ul>
        <div align="center">
          @if($signed_in == 1)
          <a href="/checkout/premium" class="button one-on-one-btn">Get One-on-One Support Today</a>
          @elseif(isset($showSignUp) && $showSignUp == 0)
          <a href="/signin{{$utm_source}}&utm_medium={{$utm_medium}}_get-one-on-one-support-today{{$utm_campaign}}{{$utm_content}}&redirect=checkout/premium{{$utm_term}}" class="button one-on-one-btn">Get One-on-One Support Today</a>
          @else
          <a href="/signup{{$utm_source}}&utm_medium={{$utm_medium}}_get-one-on-one-support-today{{$utm_campaign}}{{$utm_content}}&redirect=/get_started/9{{$utm_term}}" class="button one-on-one-btn">Get One-on-One Support Today</a>
          @endif
        </div>
      </div>
    </div>

    <div class="comments-block">
      <div class="row">
        <div class="large-12 columns text-center students-head students-head-premium-general ">
          <h1>See what other students have to say</h1>
        </div>
      </div>
      <div class="row comments-inner ">
        <div class="large-12 columns align-center owl-carousel owl-theme">
          <div class="card-dimensions-holder">
            <div class="card-dimensions">
              <div class="card card-style premium-general-card">
                <div class="row user-block1">
                  <div class="large-6 medium-6 small-4 columns image-frame1">
                    <img src="/css/PremiumIndia/images/AlveProfilePic.png" class="profile-image1">
                  </div>
                   <div class="large-6 medium-6 small-8 columns user-info1">
                      <div class="title">Alve Chowdhury </div>
                      <div>
                        <img src="/css/premiumGeneral/images/2000px-Flag_of_Bangladesh.jpg" class="profile-image2">
                        <span class="country-name">from Bangladash</span>
                      </div>
                    </div>
                </div>
                <div class="card-text-section">
                  <p> "I found Plexuss while Googling... Once you are a member of Plexuss, they consider it their responsibility to help you... If you need information about any school anywhere in the USA, Plexuss will definitely help you a lot."</p>
                </div>
                <div class="user-block">
                  <div class="info-block">
                    <div>Applied to Northeastern University</div>
                    <div>Texas A&M University and Adelphi University</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="card-dimensions-holder">
            <div class="card-dimensions">
              <div class="card card-style premium-general-card">
                <div class="row user-block1">
                  <div class="large-6 medium-6 small-4 columns image-frame1">
                    <img src="/css/PremiumIndia/images/EvanProfilePic.png" class="profile-image1">
                  </div>
                   <div class="large-6 medium-6 small-8 columns user-info1">
                      <div class="title">Evan Saber </div>
                      <div>
                        <img src="/css/PremiumIndia/images/Flag_of_Iraq.svg.png" class="profile-image2">
                        <span class="country-name">from Kirkuk, Iraq</span>
                      </div>
                    </div>
                </div>
                <div class="card-text-section">
                  <p> "Plexuss has been a motivational step to work harder to reach my goals. Every time I log into the website I start imagining and asking myself what it would be like to study abroad. It simply has inspired me to work harder, to follow my dreams, and gives me hope that anything is possible!"</p>
                </div>
                <div class="user-block">
                  <div class="info-block">
                    <div>Interested in University of Arkansas</div>
                    <div>University of Illinois-Chicago</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="card-dimensions-holder">
            <div class="card-dimensions">
              <div class="card card-style premium-general-card">
                <div class="row user-block1">
                  <div class="large-6 medium-6 small-4 columns image-frame1">
                    <img src="/css/PremiumIndia/images/University_of_Illinois_at_Chicago.png" class="profile-image1">
                  </div>
                   <div class="large-6 medium-6 small-8 columns user-info1">
                      <div class="title">Richard O'Rourke </div>
                      <div>
                        <img src="/css/PremiumIndia/images/1280px-Flag_of_the_United_States.svg.png" class="profile-image2">
                        <span class="country-name">University of Illinois at Chicago</span>
                      </div>
                    </div>
                </div>
                <div class="card-text-section">
                  <p> "No other student search service on the market offers such a wide diversity of quality prospective students and communication tools. We rely on Plexuss with our international recruitment efforts. Specially now that you can apply directly using Plexuss' platform."</p>
                </div>
                <div class="user-block">
                  <div class="info-block">
                    <div>Associate Director Office of Admission</div><div> Recruitment and Outreach</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="card-dimensions-holder">
            <div class="card-dimensions">
              <div class="card card-style premium-general-card">
                <div class="row user-block1">
                  <div class="large-6 medium-6 small-4 columns image-frame1">
                    <img src="/css/PremiumIndia/images/Humboldt_State_University.png" class="profile-image1">
                  </div>
                   <div class="large-6 medium-6 small-8 columns user-info1">
                      <div class="title">Emily Kirsch </div>
                      <div>
                        <img src="/css/PremiumIndia/images/1280px-Flag_of_the_United_States.svg.png" class="profile-image2">
                        <span class="country-name">Humboldt State University</span>
                      </div>
                    </div>
                </div>
                <div class="card-text-section">
                  <p> "I encourage every international student to use Plexuss to connect with Humboldt state University. Plexuss is the best resource I have seen in helping students find the right college. I have great trust in students who are referred to us through Plexuss. I look forward to see your online applications."</p>
                </div>
                <div class="user-block">
                  <div class="info-block">
                    <div>International Marketing and Recruitment</div><div> Coordinator</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="footer-area students-head-premium-general" align="center">
      <h1>Time's running out for the upcoming 2019 school year.</h1>
      @if($signed_in == 1)
      <a href="/checkout/premium" class="button custom-btn promise-btn ">Upgrade to Plexuss Premium Today</a>
      @elseif(isset($showSignUp) && $showSignUp == 0)
      <a href="/signin{{$utm_source}}&utm_medium={{$utm_medium}}_upgrade-to-plex-premium-today{{$utm_campaign}}{{$utm_content}}&redirect=checkout/premium{{$utm_term}}" class="button custom-btn promise-btn ">Upgrade to Plexuss Premium Today</a>
      @else
      <a href="/signup{{$utm_source}}&utm_medium={{$utm_medium}}_upgrade-to-plex-premium-today{{$utm_campaign}}{{$utm_content}}&redirect=/get_started/9{{$utm_term}}" class="button custom-btn promise-btn ">Upgrade to Plexuss Premium Today</a>
      @endif
      <a href="https://sinashayesteh.youcanbook.me/index.jsp" class="button custom-btn question-btn">Have Question? Speak to an Expert!</a>
    </div>

    <div id="footer">
      <div class="row">
        <div class="large-12 columns">
          <a href="https://plexuss.com/"><img src="/css/PremiumIndia/images/footer-logo.png" class="profile-image1"></a>
          <span class="copyright">&copy;<?php echo date('Y');?><a href ="/"> Plexuss.com</a></span>
        </div>
      </div>
    </div>

    <div class='backToTop'>
      <span class='hide-for-small-only'>
        <img src="/css/PremiumIndia/images/arrow-top.png" class="profile-image1">
      </span>
      <div class='row show-for-small-only'>
        <div class='small-8 column'>
          <span id='bttLeft'>
            <img src="/css/PremiumIndia/images/arrow-top.png" class="profile-image1">
          </span>
        </div>
      </div>
    </div>


@stop
