@extends('public.search.master')
@section('content')

<div class="row">
    <h2 class="college-by-state-headline">{{$headline}}</h2>
    <div class="state-description-container">
        <div class='content-container'>
            <img src={{$flag_image}} class="flag-image" alt="{{$flag_image_alt}}" />

            <div class="state-content" data-content="{{$state_content}}">
                {!!$state_content!!}
            </div>
        </div>

        <div class='read-more-button'>
            <div class='read-more-text'>Read More</div>
        </div>
    </div>
    <div class="row search-results-container">

        <div class="search-headning-div">       
            We’ve found <span class="f-bold fs22 c98">{{$recordcount}}</span> colleges from {{$state_name}}...


            @if(isset($dept_majors) && count($dept_majors) > 0 && $type == 'majors')
                <div class="search-major-listing-cont">
                    @foreach($dept_majors as $major)
                    <div class="search-major-listing">
                        {{ $major->name || '' }}
                        <div class="filter-majors-x">&times;</div>
                    </div>
                    @endforeach

                </div>
            @endif
        </div>
        <div class="search-content-results-div">
            @if(isset($searchData) && count($searchData)>0)
                <p class='number-of-results-on-page'>{{count($searchData)}} results on page</p>
                @foreach($searchData as $key=>$keyval)
                    <div class="row pt20">
                        <div class="large-2 small-3 column text-center">
                            @if(isset($keyval->logo_url) &&  $keyval->logo_url!='')                
                                <img src="{{$keyval->logo_url}}" class="college_logo" alt=""/>
                            @else
                                <img src="images/no_photo.jpg" class="college_logo" alt=""/>
                            @endif
                        </div>
                         <div class="large-10 small-9 column pr10">
                            @if(isset($keyval->slug))
                               <span class="c-blue fs18 f-bold"><a href="/college/{{$keyval->slug or ''}}" class="c-blue" >{{$keyval->school_name}}</a></span>&nbsp;<span class="flag flag-{{ $keyval->country_code or ''}}"> </span>
                            @endif
                            <span class="c79 fs12 d-block mt10 l-hght18">
                           
                           
                            <?php
                                $acceptance_rate='N/A';
                                if(isset($keyval->percent_admitted)){
                                     $acceptance_rate = $keyval->percent_admitted;
                                }
                            /*
                             if($keyval->applicants_total>0)    
                            {
                                $acceptance_rate=($keyval->admissions_total)/($keyval->applicants_total);
                                $acceptance_rate=number_format($acceptance_rate,1);
                            }else{
                                $acceptance_rate = $keyval->percent_admitted;
                            }
                             */
                            $plexuss_rank='N/A';
                            if(isset($keyval->rank) && $keyval->rank!='')
                            {
                                $plexuss_rank='#'.$keyval->rank;
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
                                    
                                   <!-- <li><a href="/college/{{$keyval->slug}}?type=athletics" class="c-blue" >Athletics</a></li>                                
                                    <li><a href="/college/{{$keyval->slug}}?type=programs_majors" class="c-blue" >Programs & Majors</a></li>
                                    <li><a href="/college/{{$keyval->slug}}?type=campus" class="c-blue" >Campus Life</a></li>-->
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


       <div class="row pt40 pb10">
           <div class="large-2 small-2 column no-padding"></div>
                
           <div class="large-10 small-10 column no-padding">
            <ul class="pagination">
                @if (isset($page) && is_int((int)$page) && (int)$page > 1)
                    <li>
                        <a href="/college/state/{{$slug}}?page={{$page - 1}}" rel="previous">« Previous</a>
                    </li>
                @else
                    <li class="disabled" >
                        <span>« Previous</span>
                    </li>
                @endif

                @if (isset($page) && isset($recordcount) && is_int((int)$page) && (int)($page*10) < (int)($recordcount))
                <li>
                    <a href="/college/state/{{$slug}}?page={{$page + 1}}" rel="next">Next »</a>
                </li>
                @else
                    <li class="disabled" >
                        <span>Next »</span>
                    </li>
                @endif
            </ul>

            </div>               
        </div>    
    </div>

    </div>
</div>
@stop



