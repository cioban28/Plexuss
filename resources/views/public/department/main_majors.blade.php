@extends('public.department.master_majors')
@section('content')
<style>
.quelist li{
	list-style-type: disc;
	margin-left:10px;
}
.quelist{
	margin-left:50px;
}
</style>

<div class="row">
  <div class='columns small-12 text-center'>
      <div class="center-college-nav-ranking mt30">
      @include('private.college.collegeNav')
      </div>
  </div>
</div>
    
<div class="right-bar-department-info">
  <div class="row">
    <h1 class="department-headning-div"> Find Your Major </h1>
    <div class="department-content-div">
      <div class="row">
        <div class="column large-6 small-12 main-contnet-major">
          <p> Get started on your college major search. <br />
            Explore our fields of study and major guides to learn more about your college major options. These guides offer insight on potential career options and offer advice on how to prepare for a specific major while you are still in high school. </p>
          <p class="hidden-para-mobile"> Plus, you can easily access and browse through a list of colleges offering that major. <br />
            The information provided can help you <br />
            answer the question, <strong>"what major is right for me?"</strong> </p>
        </div>
        <div class="column large-6 small-12">
          <!-- image carousel -->
          <div class="pdept-content pdept-display-container" style="max-width:800px">
		  			<img class="mySlides" alt="College Major Search" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/department/college-major-search.jpg" style="width:100%">
						<img class="mySlides" alt="Find your Major" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/department/find-your-major.jpg" style="width:100%">
						<img class="mySlides" alt="What Major is Right for Me" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/department/what-major-is-right-for-me.jpg" style="width:100%">
            <div class="pdept-center pdept-container pdept-section pdept-large pdept-text-white pdept-display-bottommiddle" style="width:100%">
              	<span class="pdept-badge demo pdept-border pdept-transparent pdept-hover-white" onclick="currentDiv(1)"></span>
						  	<span class="pdept-badge demo pdept-border pdept-transparent pdept-hover-white" onclick="currentDiv(2)"></span>
						  	<span class="pdept-badge demo pdept-border pdept-transparent pdept-hover-white" onclick="currentDiv(3)"></span>
						</div>
          </div>
          <!-- image carousel -->
        </div>
      </div>
      <div class="row">
        <div class="majors-white-box majors-container-div">
          <div class="dept-container clearfix">
						@for($i = 0; $i < 3; $i++)
            <div class="dept-box">
							<a href="/college-majors/{{$depts[$i]->slug}}">
	              <div class="dept-img {{$depts[$i]->slug}}"></div>
	              <div class="dept-name">{{$depts[$i]->name}}</div>
              </a>
						</div>
            @endfor
            <div class="majors-toggle-mobile fadeIn">
							@for($i; $i < count($depts); $i++)
              <div class="dept-box">
								<a href="/college-majors/{{$depts[$i]->slug}}">
	                <div class="dept-img {{$depts[$i]->slug}}"></div>
	                <div class="dept-name">{{$depts[$i]->name}}</div>
                </a>
							</div>
              @endfor
						</div>
	          <div>
	               <div class="majors-toggle-btn">show more...</div>
	          </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <h1 class="department-headning-div"> How to Decide on a Major </h1>
    <div class="department-content-div margin-div">
      <p> Choosing your major is a big decision. What you major in not only helps inform what courses you take while you are a student, but it can also guide your future career path. It won't lock you into a set career, but it does lock you into a lot of classroom time learning about whatever field you choose. No matter what you major in, make sure that it's something you love and can help you achieve your career goals. </p>
      <p> Some people have just always known what they want to do with their lives. Others have no clue. Whether you fall into either extreme or somewhere in the middle, the following methods will help you narrow down your college major options. </p>
      <br />
      <p><strong>Work Backwards</strong></p>
      <p>You can find your major by considering your future career options and then working backwards from there. For example, if you are interested in occupational therapy (OT), you might go shadow a professional at your local hospital. Doing this would allow you to learn more about the job and the therapist's career path. </p>
      <p>You may come to realize that several different majors would prepare you for an OT graduate school program or career. Explore these majors until you find one that suits you.</p>
      <br />
      <p><strong>Consider your Strengths</strong></p>
      <p>You can also find your major by evaluating your strengths. Your strengths are not just the school subjects that you excel in. They could be your personality, the job you rock at, or the volunteer work that you love. With so many college major options, you'll have no trouble finding and choosing a major that matches your unique strengths. </p>
      <p>If your strength is that you love tutoring grade school kids, then education might be the right major for you. If you pride yourself on your customer service skills, then hospitality and tourism management could be a good fit. Are you an A+ physics student? Consider engineering or physics.</p>
      <br />
      <p><strong>Think about Your Desired Lifestyle</strong></p>
      <p>Aside from just thinking about your future career, you should also think about what you want your life to look like.  Ponder over these questions to get started:</p>
      <div class="row">
        <ul class="quelist">
          <li>Can you handle complicated and rigorous course loads each semester? </li>
          <li>What is the return-on-investment (ROI) for the major you are thinking about? </li>
          <li>Is making a lot of money important to you? How much money do you want to make? </li>
          <li>Do you want your life to be about work or about work-life balance? </li>
          <li>How many hours are you willing to work every week? </li>
          <li>Are you willing to travel for work? How often? </li>
          <li>Are you okay with working at a desk or do you want to be on your feet? </li>
          <li>Do you prefer working solo or in groups? </li>
        </ul>
      </div>
      <br />
      <p>There is no wrong answer to these questions. Everyone answers them differently. Use these answers to help you decide what to study in college. For example, if you want to make a lot of money, majors like social work, parks, recreation and leisure studies, and education probably won't make the cut </p>
      <br />
      <p><strong>Remember, It's Okay If You're Undecided</strong></p>
      <p>Finding the right major and career takes time. It's okay if you're still not sure what to study in college. Colleges allow students to come in as an undeclared or undecided major. This way, students can explore their options up close before choosing a major. You'll have a chance to take classes, speak with a faculty mentor, and think more about your future. Most colleges just ask that students declare a major prior to the start of their sophomore year. . </p>
    </div>
  </div>
</div>
<script>
var slideIndex = 1;
showDivs(slideIndex);

function plusDivs(n) {
  showDivs(slideIndex += n);
}

function currentDiv(n) {
  showDivs(slideIndex = n);
}

function showDivs(n) {
  var i;
  var x = document.getElementsByClassName("mySlides");
  var dots = document.getElementsByClassName("demo");
  if (n > x.length) {slideIndex = 1}
  if (n < 1) {slideIndex = x.length}
  for (i = 0; i < x.length; i++) {
     x[i].style.display = "none";
  }
  for (i = 0; i < dots.length; i++) {
     dots[i].className = dots[i].className.replace(" pdept-white", "");
  }
  x[slideIndex-1].style.display = "block";
  dots[slideIndex-1].className += " pdept-white";
}
var nslide=1;
 setInterval(function(){currentDiv(nslide);nslide++;if(nslide>3) nslide=1;}, 2000);
</script>
@stop
