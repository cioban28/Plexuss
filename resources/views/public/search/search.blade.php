@extends('public.search.master')

@section('content')

{{-- */$type = isset($type)?$type:''/* --}}
{{-- */$searchData = isset($searchData)?$searchData:''/* --}}
{{-- */$term = isset($term)?$term:''/* --}}
{{-- */$recordcount = isset($recordcount)?$recordcount:''/* --}}

<?php //dd($searchData);

//placeholder for testing ui
$dept_majors = array();
$dept_majors[0] = 'major A';
$dept_majors[1] = 'major B';
$dept_majors[2] = 'major C';

//will want to recieve schools in groups of majors 
//major (ex art History) -> (Academy of art, ect..)
//major (ex Performance Art) => (schoolname,...)

//will have flags for which ones are on in php here and will ?


?>
<div class="row">
	

    <!-- results for section -->
	<div class="search-headning-div">    	
        We’ve found <span class="f-bold fs22 c98">{{$recordcount}}</span> results for {{$term}}...


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
    

    <!-- ////////////// search content /////////////// -->
    <div class='search-content-div'>
     
    	@if($type!='default')
            <div class="fs18 c79 pb10 mr5 search-content-title">{{$type}}</div>
        @endif
       
        @if($type=='college')	  		
        

          	@if(count($searchData)>0)
                @foreach($searchData as $key=>$keyval)
                    <div class="row pt20">
                        <div class="large-2 small-3 column text-center">
                            @if($keyval->logo_url!='' && $keyval->id != 1785)                
                                <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/{{$keyval->logo_url}}" class="college_logo" alt=""/>
                            @else
                                <img src="images/no_photo.jpg" class="college_logo" alt=""/>
                            @endif
                        </div>
                         <div class="large-10 small-9 column pr10">
                            <span class="c-blue fs18 f-bold"><a href="/college/{{$keyval->slug}}" class="c-blue" >{{$keyval->school_name}}</a></span>
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
    						if($keyval->plexuss!='')
    						{
    							$plexuss_rank='#'.$keyval->plexuss;
    						}
        					?>
                            
                                Acceptance rate: {{$acceptance_rate}}%  |  In-state Tuition: ${{number_format($keyval->tuition_avg_in_state_ftug,0,"-",",")}}  
                                |  Total Enrolled Students: {{number_format($keyval->undergrad_total,0,"-",",")}}  |   Plexuss Rank <?php echo $plexuss_rank?> <br /> {{$keyval->city}},
                                <span class="f-bold">{{$keyval->state}}</span>  |  <span class="c-blue fs12 quick-linker" style="cursor:pointer;" onclick="expandDivContent('quick-link-div-{{$keyval->id}}','quick-link-{{$keyval->id}}');">open quick links  <span class="expand-toggle-span run" id="quick-link-div-{{$keyval->id}}">&nbsp;</span> </span>
                            </span>                      
                        
                            <div class="row d-none" id="quick-link-{{$keyval->id}}"> 
                                <ul class="quick-link-ul">
                                    <li><a href="/college/{{$keyval->slug}}/admissions" class="c-blue" >Admissions</a></li>
                                    <li><a href="/college/{{$keyval->slug}}/ranking" class="c-blue" >Ranking</a></li>                                
                                    <li><a href="/college/{{$keyval->slug}}/financial-aid" class="c-blue" >Financial Aid</a></li>
                                    
                                   <!-- <li><a href="/college/{{$keyval->slug}}?type=athletics" class="c-blue" >Athletics</a></li>                                
                                    <li><a href="/college/{{$keyval->slug}}?type=programs_majors" class="c-blue" >Programs & Majors</a></li>
                                    <li><a href="/college/{{$keyval->slug}}?type=campus" class="c-blue" >Campus Life</a></li>-->
                                </ul>
                                                </div>
                        
                        </div>
                    </div>
                   
                @endforeach        
            @else
            	<div class="row pt20 c79" style="text-align:center;vertical-align:middle">No Record Founds..</div>
            @endif
        
        @elseif($type=='ranking')            
            <div class="row pt20">
                <div class="large-1 small-1 column no-padding">
                    <img src="images/ranking_icon.png" alt=""/>
                </div>
                 <div class="large-11 small-10 column no-padding m-pr10">
                    <span class="c-blue fs18 f-bold">Top Grad School Programs</span>
                    <span class="c79 fs12 d-block mt10 l-hght18"> Georgia Tech comes in at #3 
                        <span class="f-bold clr-orange fs12">...See full list</span> 
                    </span>            	
                
                </div>
            </div> 
            
        @elseif($type=='news') 
            @if(count($searchData)>0)
                @foreach($searchData as $key=>$keyval)  
                <div class="row pt20">
                    <div class="large-1 small-1 column no-padding">
            
                    @if($keyval->img_sm!='')                
                    <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/{{$keyval->img_sm}}" alt="{{$keyval->external_name}}" title="{{$keyval->external_name}}" class="news_logo"/>
                    @else
                    <img src="images/no_photo.jpg" class="college_logo" alt=""/>
                    @endif
                        
                    </div>
                     <div class="large-11 small-10 column no-padding m-pr10">
                        <span class="c-blue fs18 f-bold">{{$keyval->external_name}}</span>
                        <span class="c79 fs12 d-block mt10 l-hght18">
                            {{substr(strip_tags($keyval->content),0,250)}}
                            <span class="f-bold  fs12"><a href="/news/article/{{$keyval->slug}}" class="clr-orange" >...Read More</a></span> 
                        </span>
                        
                    
                    </div>
                </div>
                @endforeach            
            @else
        	<div class="row pt20 c79" style="text-align:center;vertical-align:middle">No Record Founds..</div>
            
            @endif
        @elseif($type=='students')

            @if(count($searchData)>0)
                @foreach($searchData as $key => $keyval)

                    <?php 
                        //dd($key);

                        if($keyval->vhStat == '' && $keyval->vaStat == '' && $keyval->pstat == ''){
                    
                            if($keyval->status == 1 && $keyval->user_recruit == 1 && $keyval->college_recruit == 0){
                                $tab = 'Inquiries';
                                $type = 'inquiries';
                            }
                            else if($keyval->status == 1 && $keyval->user_recruit == 0 && $keyval->college_recruit == 1){
                                $tab = 'Pending';
                                $type = 'inquiries/pending';
                            }
                            else if($keyval->status == 1 && $keyval->user_recruit == 1 && $keyval->college_recruit == 1){
                                $tab= 'Handshakes';
                                $type = 'inquiries/approved';
                            }
                            else if($keyval->status == 0 && $keyval->user_recruit < 9 && $keyval->college_recruit < 9){
                                $tab = 'Removed';
                                $type = 'inquiries/removed';
                            }
                            else if($keyval->status == 1 && $keyval->user_recruit == 1 && $keyval->college_recruit == -1){
                                $tab = 'Rejected';
                                $type=  'inquiries/rejected';
                            }

                        }else{

                            if($keyval->vhStat == 'vh'){
                                $tab = 'Verified Handshakes';
                                $type= 'inquiries/verifiedHs';
                            }else if($keyval->vaStat == 'va'){
                                $tab = 'Verified Application';
                                $type= 'inquiries/verifiedApp';
                            }else if($keyval->pstat == 'p'){
                                $tab = 'Prescreened';
                                $type = 'inquiries/prescreened';
                            }
                        }
                        $type .='?uid='.Crypt::encrypt($keyval->id);
                    ?>

                    <div class="row pt20">
                        
                        <div class="large-2 small-3 column text-center">

                            <div class="students-link">
                                <a href="/admin/{{$type}}">   
                                    @if(!empty($keyval->profile_img_loc))               
                                        <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/{{ $keyval->profile_img_loc}}" class="college_logo" alt=""/>
                                    @else
                                        <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png" class="college_logo" alt=""/>
                                    @endif
                                </a>
                            </div>
                        </div>
                        
                        <div class="large-10 small-9 column pr10">
                        
                            
                            <span class="c-blue fs18 f-bold">
                                <a href="/admin/{{$type}}">
                                    <div class="students-link bluetxt" class="c-blue" >{{$keyval->fname.' '.$keyval->lname}}</div> 
                                </a>
                            </span>

                            <span class="c79 fs12 d-block mt10 l-hght18">
                                <span class="f-bold">{{$keyval->email or 'N/A'}}   &nbsp; |  &nbsp;  {{$keyval->phone or 'N/A'}} |  &nbsp; {{$tab or ''}}</span> 
                            </span>  

                       
                        </div>
                    </div>


              
                   
                @endforeach        
            @else
                <div class="row pt20 c79" style="text-align:center;vertical-align:middle">No Record Founds..</div>
            @endif
        @else
      
        
            {{-- */$current_cat = ''/* --}}
            {{-- */$old_cat = ''/* --}}



            @if(count($searchData)>0)          
             @foreach($searchData as $key => $keyval)
             {{-- */$current_cat = $keyval->category/* --}}
             
                @if(isset($old_cat) && $current_cat!=$old_cat)          
                <div class="fs18 c79 pb10 mr5 " style="border-bottom:#797979 solid 1px; text-transform:capitalize">{{$current_cat}}
                <span class="d-block pos-abs fs14" style="right:29px; margin-top:-10px;color:#98D0EF;"><a href="/search?type={{$current_cat}}&term={{$term}}">more</a></span>
                </div>
                @endif 
                
            	 @if($keyval->category=='college')            	             
              		     <div class="row pt20">
                    <div class="large-2 small-2 column no-padding">
                        @if($keyval->logo_url!='' && $keyval->id != 1785)                
                            <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/{{$keyval->logo_url}}" class="college_logo" alt=""/>
                        @else
                            <img src="images/no_photo.jpg" class="college_logo" alt=""/>
                        @endif
                    </div>
                     <div class="large-10 small-9 column no-padding pr10">
                        <span class="c-blue fs18 f-bold"><a href="/college/{{$keyval->slug}}" class="c-blue" >{{$keyval->school_name}}</a></span>
                        <span class="c79 fs12 d-block mt10 l-hght18">

                            <?php
                            /*check to make sure you aren't dividing by zero*/
                            //dd($keyval);
                                if($keyval->applicants_total != 0){
                                    $acceptanceRate = round((float)($keyval->admissions_total / $keyval->applicants_total) * 100 );
                                }else{
                                    $acceptanceRate = 'N/A';
                                }
                                //enrollment shouldn't be 0, so check if enrollment value is 0
                                if($keyval->undergrad_total != 0){
                                    $enrolled = number_format($keyval->undergrad_total,0,"-",",");
                                }else{
                                    $enrolled = 'N/A';
                                }

                                $tuition = number_format($keyval->tuition_avg_in_state_ftug,0,"-",",");
                                
                            ?>
                        
                          Acceptance rate: {{$acceptanceRate or 'N/A'}}%  |  In-state Tuition: ${{$tuition or 'N/A'}}  
                            |  Total Enrolled Students: {{$enrolled or 'N/A'}}  |   Plexuss Rank #{{$keyval->plexuss or 'N/A'}} <br /> {{$keyval->city}},
                            <span class="f-bold">{{$keyval->state}}</span>  |  <span class="c-blue fs12 quick-linker" style="cursor:pointer;" onclick="expandDivContent('quick-link-div-{{$keyval->id}}','quick-link-{{$keyval->id}}');">open quick links  <span class="expand-toggle-span run" id="quick-link-div-{{$keyval->id}}">&nbsp;</span> </span>
                        </span>
                      
                    
                        <div class="row d-none" id="quick-link-{{$keyval->id}}"> 
                            <ul class="quick-link-ul">
                                <li><a href="/college/{{$keyval->slug}}/admissions" class="c-blue" >Admissions</a></li>
                                <li><a href="/college/{{$keyval->slug}}/ranking" class="c-blue" >Ranking</a></li>                                
                                <li><a href="/college/{{$keyval->slug}}/financial-aid" class="c-blue" >Financial Aid</a></li>
                            </ul>
                        </div>
                    
                    </div>
                </div>
                <script type="text/javascript">
                    $('#quick-link-div-{{$keyval->id}}').click(function(){ expandDivContent('quick-link-div-{{$keyval->id}}','quick-link-{{$keyval->id}}')});
                </script> 
              
              	
              
                 @elseif($keyval->category=='news')                        
                 <div class="row pt20">
                <div class="large-1 small-1 column no-padding">
        
                @if($keyval->img_sm!='')                
                <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/{{$keyval->img_sm}}" alt="{{$keyval->external_name}}" title="{{$keyval->external_name}}" class="news_logo"/>
                @else
                <img src="images/no_photo.jpg" class="college_logo" alt=""/>
                @endif
                    
                </div>
                 <div class="large-11 small-10 column no-padding m-pr10">
                    <span class="c-blue fs18 f-bold"><a href="/{{$keyval->category}}/{{$keyval->id}}">{{$keyval->external_name}}</a></span>
                    <span class="c79 fs12 d-block mt10 l-hght18">
                        {{substr(strip_tags($keyval->content),0,250)}}
                        <span class="f-bold  fs12"><a href="/news/{{$keyval->id}}" class="clr-orange" >...Read More</a></span> 
                    </span>
                    
                
                </div>
            </div>
                 @endif	 

                 {{-- */$old_cat = $keyval->category/* --}}
            @endforeach
            
            @else
        	<div class="row pt20 c79" style="text-align:center;vertical-align:middle">No Record Founds..</div>
            
            @endif
            
                       
        @endif
        
        

       <div class="row pt40">
           <div class="large-2 small-2 column no-padding"></div>
                
           <div class="large-10 small-10 column no-padding">
			<?php 
			if($type=='college' || $type=='news' ||  $type=='students')
			{
				echo $searchData->appends($querystring)->links();
				//echo $searchData->appends(array('type' => 'college','term'=>'aa'))->links();
            }?>
               
            </div>               
	</div>    
</div>
</div>
@stop



