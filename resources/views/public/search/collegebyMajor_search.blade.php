@extends('public.search.master')

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
<div class="row">
    
    <!-- results for section -->
    <div class="search-headning-div">       
        We’ve found <span class="f-bold fs22 c98 recordCount">{{$recordcount or '0'}}</span> results for <span class="term-searched">{{$searchterm or ''}}</span>...

        <!-- <div class="majors-tags-container"></div> -->

        @if($major_tags && count($major_tags) > 0)
        <?php 
            $len = count($major_tags);
            $showNum = min($len, 5);
            $i = 0;
            // if(!isset($major_tags[0])) 
            //     $i =1;
            // print_r($major_tags);
            //some reason array gets returned starting at index 1... if no term set
        ?>
        <div class="majors-tags-container">
            <div class="search-major-listing-cont">
                <div class="search-major-listing fadeIn all-major-tags clear-all-colors" data-mid="-1">
                    Clear All
                    <div class="filter-majors-x">
                        &times;
                    </div>
                </div>
                @for($i; $i < $showNum; $i++)
                    <div class="search-major-listing fadeIn" data-mid="{{$major_tags[$i]->id}}">
                        {{$major_tags[$i]->name}}
                        <div class="filter-majors-x">
                            &times;
                        </div>
                    </div>
                @endfor
            </div>


            @if($len > 5)
            
                <div class="more-majors-results">
                    @for($i; $i < $len; $i++)
                       <div class="search-major-listing fadeIn" data-mid="{{$major_tags[$i]->id}}">
                            {{$major_tags[$i]->name}}
                            <div class="filter-majors-x">
                                &times;
                            </div>
                        </div> 
                    @endfor
                </div>

                <div class="toggle-majors-results">
                    show remaining majors ({{ $len - $showNum }})
                </div>
                <div class="toggle-majors-arrow">&#8250;</div>
            
            @endif

        </div>
        @endif


    </div>
    
	
    <!-- ////////////// search content /////////////// -->
    <div class='search-content-div'>
        <div class="search-content-results-div">
            @if(isset($searchData) && count($searchData)>0)
                @foreach($searchData as $key=>$keyval)
                    <div class="row pt20">
                        <div class="large-2 small-3 column text-center">
                            @if(isset($keyval->logo_url) &&  $keyval->logo_url!='' && $keyval->id != 1785)                  
                                <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/{{$keyval->logo_url}}" class="college_logo" alt=""/>
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
                            if(isset($keyval->plexuss) && $keyval->plexuss!='')
                            {
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


       <div class="row pt40">
           <div class="large-2 small-2 column no-padding"></div>
                
           <div class="large-10 small-10 column no-padding">
            <?php 
                if($type=='college' || $type=='majors' || $type=='news' ||  $type=='students'){
                    echo $searchData->appends($querystring)->links();
                    //echo $searchData->appends(array('type' => 'college','term'=>'aa'))->links();
                }
            ?>
               
            </div>               
    </div>    
</div>
</div>
@stop



