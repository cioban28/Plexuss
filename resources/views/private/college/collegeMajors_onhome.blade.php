<!-- Majors -->


<div class="majors-white-box">
     <div class="header">Find a school based on major</div>

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
