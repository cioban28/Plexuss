@extends('public.department.master')

@section('content')

{{-- */$type = isset($type)?$type:''/* --}}
{{-- */$searchData = isset($searchData)?$searchData:''/* --}}
{{-- */$term = isset($term)?$term:''/* --}}
{{-- */$recordcount = isset($recordcount)?$recordcount:''/* --}}

<?php

// dd( get_defined_vars() );
//placeholder for testing ui
// $dept_majors = array();
// $dept_majors[0] = 'major A';
// $dept_majors[1] = 'major B';
// $dept_majors[2] = 'major C';

//will want to recieve schools in groups of majors
//major (ex art History) -> (Academy of art, ect..)
//major (ex Performance Art) => (schoolname,...)

//will have flags for which ones are on in php here and will ?


  ?>
  <div class="right-bar-department-info">
    <div class="row">
          <h1 class="department-headning-div">
              {{ $metainfo->department_category }}
          </h1>
          <div class="department-header-img">
              <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/department/header/{{ $metainfo->header_img_name }}" alt="{{ $metainfo->header_image_alt }}" class="full-width" />
			    </div>
      </div>
      <?php 
      $cnt = count($majors_for_department);
      $counter =0;
      ?>
      @if(isset($majors_for_department) && !empty($majors_for_department[0]))
        <div class="row department-major-buttons">
          @for($i = 0; $i < 3; $i++)
            <div class="column small-12 medium-4 content">
              <a class="button" href="/college-majors/{{$majors_for_department[$i]->mdd_slug}}/{{$majors_for_department[$i]->slug}}">
                  {{$majors_for_department[$i]->name}}
                </a>
            </div>
          @endfor
          <div class="majors-toggle-mobile fadeIn">
            @for($i; $i < count($majors_for_department); $i++)
              <div class="column small-12 medium-4 content">  
                <a class="button" href="/college-majors/{{$majors_for_department[$i]->mdd_slug}}/{{$majors_for_department[$i]->slug}}">
                    {{$majors_for_department[$i]->name}}
                  </a>
              </div>
            @endfor
          </div>
          <div>
            <div class="majors-toggle-btn">show more...</div>
          </div>
        </div>
      @endif

      <div class="row">
          <h1 class="department-headning-div">
              {{ $metainfo->headline1 }}
          </h1>
          <div class="department-content-div margin-div">
            {!! $metainfo->content1 !!}
			    </div>
      </div>
      <div class="row">
          <h1 class="department-headning-div">
              {{ $metainfo->headline2 }}
          </h1>
          <div class="department-content-div margin-div">
            {!! $metainfo->content2 !!}
			    </div>
      </div>
      <div class="row">
        <div class="department-header-img">
            <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/department/images/{{ $metainfo->body_img1_name }}" alt="{{ $metainfo->body_img1_alt }}" class="full-width" />
        </div>
      </div>
      <div class="row">
          <h1 class="department-headning-div">
              {{ $metainfo->headline3 }}
          </h1>
          <div class="department-content-div margin-div">
            {!! $metainfo->content3 !!}
            @foreach($majors_for_department as $mfd)
            <p><a style="font-weight: 900;font-size: 1.1em;text-decoration: underline;" href="/college-majors/{{$mfd->mdd_slug}}/{{$mfd->slug}}">{{$mfd->name}}</a>: {{$mfd->overview}}</p>
            @endforeach
			    </div>
      </div>
      <div class="row">
          <h1 class="department-headning-div">
              {{ $metainfo->headline4 }}
          </h1>
          <div class="department-content-div margin-div">
            {!! $metainfo->content4 !!}
			    </div>
      </div>
      <div class="row">
          <h1 class="department-headning-div">
              {{ $metainfo->headline5 }}
          </h1>
          <div class="department-content-div margin-div">
            {!! $metainfo->content5 !!}
			    </div>
      </div>
      <div class="row">
          <h1 class="department-headning-div">
              {{ $metainfo->headline6 }}
          </h1>
          <div class="department-content-div margin-div">
            {!! $metainfo->content6 !!}
			    </div>
      </div>
  </div>



    <div class="colleges-list-div">
      <div class='search-content-div'>
          <div class="search-content-results-div">
              @if(isset($searchData) && count($searchData)>0)
                  @foreach($searchData as $key=>$keyval)
                      <div class="row pt20">
                          <div class="large-2 small-3 column text-center">
                              @if(isset($keyval->logo_url) &&  $keyval->logo_url!='')
                                  <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/{{$keyval->logo_url}}" class="college_logo" alt=""/>
                              @else
                                  <img src="/images/no_photo.jpg" class="college_logo" alt=""/>
                              @endif
                          </div>
                           <div class="large-10 small-9 column pr10">
                              @if(isset($keyval->slug))
                                 <span class="c-blue fs18 f-bold"><a href="/college/{{$keyval->slug or ''}}" class="c-blue" >{{$keyval->school_name}}</a></span>&nbsp;<span class="flag flag-{{ $keyval->country_code or ''}}"> </span>
                              @endif
                              <span class="c79 fs12 d-block mt10 l-hght18">

                              <?php
                                  $acceptance_rate='N/A';
                                  if(isset($keyval->percent_admitted)) {
                                       $acceptance_rate = $keyval->percent_admitted;
                                  }
                                  $plexuss_rank='N/A';
                                  if(isset($keyval->plexuss) && $keyval->plexuss!='') {
                                      $plexuss_rank='#'.$keyval->plexuss;
                                  }
                              ?>

                                  Acceptance rate: {{$acceptance_rate}}%  |  In-state Tuition: ${{number_format($keyval->tuition_avg_in_state_ftug,0,"-",",")}}
                                  |  Total Enrolled Students: {{number_format($keyval->undergrad_total,0,"-",",")}}  |   Plexuss Rank <?php echo $plexuss_rank?> <br /> {{$keyval->city}},
                                  <span class="f-bold">{{$keyval->state}}</span>  |  <span class="c-blue fs12 quick-linker" style="cursor:pointer;" onclick="expandDivContent('quick-link-div-{{$keyval->id}}','quick-link-{{$keyval->id}}');">open quick links  <span class="expand-toggle-span run" id="quick-link-div-{{$keyval->id}}">&nbsp;</span> </span>
                              </span>

                              <div class="row d-none" id="quick-link-{{$keyval->id}}">
                                  @if(isset($keyval->slug))
                                    <ul class="quick-link-ul">
                                        <li><a href="/college/{{$keyval->slug}}/admissions" class="c-blue" >Admissions</a></li>
                                        <li><a href="/college/{{$keyval->slug}}/ranking" class="c-blue" >Ranking</a></li>
                                        <li><a href="/college/{{$keyval->slug}}/financial-aid" class="c-blue" >Financial Aid</a></li>
                                    </ul>
                                  @endif
                              </div>
                          </div>
                      </div>

                  @endforeach
              @else
                  <div class="row pt20 c79" style="text-align:center;vertical-align:middle">No Records Found..</div>
              @endif
          </div>
        </div>

        <div class="row pt40">
            <div class="large-2 small-2 column no-padding"></div>
            <div class="large-10 small-10 column no-padding"><ul class="pagination">
                    <li class="disabled"><span>« Previous</span></li>
                    <li><a href="/search?school_name=&country=&state=&city=&zipcode=&degree=&department={{$selected}}&imajor=&locale=&tuition_max_val=0&enrollment_min_val=0&enrollment_max_val=0&applicants_min_val=0&applicants_max_val=0&min_reading=&max_reading=&min_sat_math=&max_sat_math=&min_act_composite=&max_act_composite=&religious_affiliation=&type=college&term=&myMajors=&page=2" rel="next">Next »</a></li>
            </ul>
            </div>
 	      </div>
      </div>
@stop
