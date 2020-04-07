{{-- */$city = isset($querystring['city'])?$querystring['city']:''/* --}}
{{-- */$state = isset($querystring['state'])?$querystring['state']:''/* --}}
{{-- */$locale = isset($querystring['locale'])?$querystring['locale']:''/* --}}
{{-- */$religious_affiliation = isset($querystring['religious_affiliation'])?$querystring['religious_affiliation']:''/* --}}

{{-- */$zipcode = isset($querystring['zipcode'])?$querystring['zipcode']:''/* --}}
{{-- */$degree = isset($querystring['degree'])?$querystring['degree']:''/* --}}
{{-- */$campus_housing = isset($querystring['campus_housing']) && $querystring['campus_housing']?true:false/* --}}

{{-- */$min_reading = isset($querystring['min_reading'])?$querystring['min_reading']:''/* --}}
{{-- */$max_reading = isset($querystring['max_reading'])?$querystring['max_reading']:''/* --}}

{{-- */$min_sat_math = isset($querystring['min_sat_math'])?$querystring['min_sat_math']:''/* --}}
{{-- */$max_sat_math = isset($querystring['max_sat_math'])?$querystring['max_sat_math']:''/* --}}

{{-- */$min_act_composite = isset($querystring['min_act_composite'])?$querystring['min_act_composite']:''/* --}}
{{-- */$max_act_composite= isset($querystring['max_act_composite'])?$querystring['max_act_composite']:''/* --}}

{{-- */$miles_range_min_val = isset($querystring['miles_range_min_val'])?$querystring['miles_range_min_val']:'0'/* --}}
{{-- */$miles_range_max_val = isset($querystring['miles_range_max_val']) && $querystring['miles_range_max_val']!=''?$querystring['miles_range_max_val']:250/* --}}

{{-- */$tuition_max_val = isset($querystring['tuition_max_val']) && $querystring['tuition_max_val']!=''?$querystring['tuition_max_val']:20000/* --}}

{{-- */$enrollment_min_val = isset($querystring['enrollment_min_val'])?$querystring['enrollment_min_val']:'0'/* --}}
{{-- */$enrollment_max_val = isset($querystring['enrollment_max_val']) && $querystring['enrollment_max_val']!=''?$querystring['enrollment_max_val']:5000/* --}}

{{-- */$applicants_min_val = isset($querystring['applicants_min_val'])?$querystring['applicants_min_val']:'0'/* --}}
{{-- */$applicants_max_val = isset($querystring['applicants_max_val']) && $querystring['applicants_max_val']!=''?$querystring['applicants_max_val']:5000/* --}}

{{-- */$page= isset($page)?$page:''/* --}}
<style type="text/css">
  input[type="text"]{
    border-radius:2px !important;
  }
</style>

<?php
// dd( get_defined_vars() );

?>

<div class='row'>
  <div class='column small-12'>
    {{ Form::open(array('action' =>'SearchController@index', 'data-abide' , 'id'=>'advancesearchform', 'class' =>'side-bar-news radius-4','method' => 'get')) }}
      <div class="row">
        <div class="small-12 columns adv-search-heading">Advanced College Search</div>
        <div class="small-6 columns clear-filter text-center">

        	<input type="button" value="Clear form"  style="background:none; box-shadow:none; border:none; text-decoration:underline; padding:0" onclick="formReset('advancesearchform');"/>
             {{-- <a href="search?type={{$type}}&amp;term=" class="c-white">Clear Filter</a> --}}
        </div>
        <!--<div class="small-6 columns guide-me-filter text-center pt2">Guide me!</div>-->

       <?php
          if($type == 'college' && isset($term)){
              $sName = $term;
          }else if(isset($querystring['school_name'])){
              $sName = $querystring['school_name'];
          }else{
              $sName = '';
          }
       ?>
      </div>
       <div class="row">
        <div class="small-12 adv-search-form bold-font column" style="border-radius: 2px;">Name</div>
        <div class="small-12 column">
          <input name="school_name" type="text" value="{{$sName or ''}}" placeholder="School Name" />
        </div>
      </div>


      <div class="row">
        <div class="small-12 adv-search-form bold-font column">Country</div>
        <div class="small-12 column">
           <!--{{ Form::select('country',array('' =>'Select Country', 'US'=>'United States', 'UK'=>'United Kingdom', 'CA'=>'Canada', 'AU'=>'Australia', 'GR'=>'Germany'),'',array('class'=> 'styled-select','id'=>'country-select-box','onchange'=>'AjaxSelectBox("state","getSelectBoxVal","state-select-box","",this.value);') )}}-->
		 {{ Form::select('country',array('&nbsp;'),'',array('class'=> 'styled-select','id'=>'country-select-box','onchange'=>'CheckisUs(this.value);') )}}
        </div>
      </div>


      <div class="row">
        <div class="small-12 adv-search-form bold-font column">State</div>
        <div class="small-12 column" id="state_div">
          {{ Form::select('state',array('&nbsp;'),'',array('class'=> 'styled-select','id'=>'state-select-box','onchange'=>'AjaxSelectBox("city","getSelectBoxVal","city-select-box","",this.value);') )}}
        </div>
      </div>


      <div class="row">
        <div class="small-12 adv-search-form bold-font column">City</div>
        <div class="small-12  column" id="city_div">
          {{ Form::select('city',array('' => 'No preference'),'',array('class'=> 'styled-select','id'=>'city-select-box')) }}
        </div>
      </div>

      <div class='row'>
      <?php
        if(isset($zipcode) && $zipcode!='' && $zipcode!='')
        	 {$classdisplay='d-block'; $disable='';}
		else
			 {$classdisplay='d-none';$disable='disabled="disabled"'; $zipcode = null;}
	   ?>

        <div class='column small-5 adv-search-form bold-font'>
          Zip Code
        </div>
         <div id="miles_range" class='column small-7 adv-search-form bold-font {{$classdisplay}}'>
            Within<br/>0-250 miles
         </div>
      </div>


      <div class="row">
        <div class="small-12 columns">
    	     <div class="small-12 columns no-padding">
              {{ Form::text('zipcode',$zipcode, array('placeholder' => 'Zip Code','class'=>'advansed-search-txt','id'=>'zipcode-search-txt','onkeypress'=>'return checkZipcode(event);')) }}
            </div>
            <div class="small-6 columns no-padding">
              <div class="slider-range mt5 {{$classdisplay}}" id="slider-range-0" style="margin-left:-2px;"></div>
                <div class="small-12 mt5 text-center">
                   <input type="hidden" name="miles_range_min_val" id="miles_range_min_val" class="range-txt" <?php echo $disable?>>
                   <input type="hidden" name="miles_range_max_val" id="miles_range_max_val" class="range-txt" <?php echo $disable?>>
               </div>
            </div>
        </div>
      </div>


      <!--<div class="row">
        <div class="small-12 adv-search-form bold-font column">Areas of Study</div>
        <div class="small-12 column">
          {{ Form::select('miles', array('1' => 'No preference', '2' => 'New York','3'=>'California','4'=>'Washington D.C'), '1',array('class'=> 'styled-select') ) }}
        </div>
      </div>-->
      <!--<div class="row">
        <div class="small-12 adv-search-form bold-font column">Programs / Majors</div>
        <div class="small-12 column">
          {{ Form::select('programme', array('1' => 'No preference', '2' => 'Healthcare','3'=>'Tada','4'=>'ASdsad'), '1',array('class'=> 'styled-select') ) }}
        </div>
      </div>-->
      <div class="row" style="border-bottom: solid 2px #ffffff; margin-bottom:5px;">
        <div class="small-12 adv-search-form bold-font column"> Degree Type</div>
        <?php
              //source of degree from different places...
              if(isset($degree)) {
                $degree = $degree;
              }else if (isset($querystring['degree'])){
                $degree = $querystring['degree'];
              }else{
                $degree = '';
              }
        ?>
        <div class="small-12 column">
          {{ Form::select('degree', array('' => 'Select Degree Type','bachelors_degree' => 'Bachelors Degree','masters_degree' => 'Masters Degree',
          'post_masters_degree'=>'Post Masters Degree','doctors_degree_research'=>'Doctors Degree Research','doctors_degree_professional'=>'Doctors Degree Professional')
          ,$degree,array('class'=> 'styled-select') ) }}
        </div>
      </div>


      @if(isset($depts_cat))
        <div class="row" >
          <div class="small-12 adv-search-form bold-font column">Department</div>
          <?php
                if(isset($department)){
                  $dept = $department;
                }
                else if (isset($term) && $type == "majors") {
                  $dept = $term;
                }
                else{
                  $dept = '';
                }
          ?>

          <div class="small-12 column">
            <select value="{{$dept}}" name="department" class="styled-select dept-select-box">
                  <option value="" disabled="disabled"  @if($dept == '' || !isset($term) ) selected="selected" @endif>Select a Department...</option>
                @foreach($depts_cat as $d)
                  <option value={{$d->url_slug}} @if($dept == $d->url_slug) selected="selected" @endif>{{$d->name or ''}}</option>
                @endforeach
            </select>
          </div>
        </div>



        <div class="majors-select-container row @if(!isset($dept) || $dept == '') hide @endif">
          <div class="small-12 adv-search-form bold-font column">Major  <span><div class="sm-wh-loader hide"></div></span> </div>
          <?php if (isset($querystring['imajor'])) {
                  $imajor = $querystring['imajor'];
                } else{
                  $imajor = null;
                }
          ?>
          <div class="small-12 column">
            <select class="styled-select adv-c-s-majors-select" name="imajor" value="{{$imajor}}">
               <option value="">Select Major...</option>
               @if(isset($majors_for_cat ))
                 @foreach($majors_for_cat as $m)
                  <option value="{{$m->id or ''}}"  @if($imajor == $m->id) selected="selected" @endif>{{$m->name or ''}}</option>
                 @endforeach
               @endif
            </select>

          </div>
        </div>
      @endif




         <!-- <div class="row major-degree-type-select  @if(!isset($major_tags) || $major_tags == false) hide @endif" > -->
          <?php
            // if(isset($degree_type)){
            //   $degree_type = $degree_type;
            // }else{
            //   $degree_type = '-1';
            // }
          ?>
          <?php
         //  <div class="small-12 adv-search-form bold-font column">Degree Type of Major</div>
         //  <div class="small-12 column">
         //    {{ Form::select('degree_type', array('-1' => 'All','3' => 'Bachelors Degree','4' => 'Masters Degree')
         //    ,$degree_type,array('class'=> 'styled-select') ) }}
         //  </div>
         // <div>
         ?>




      <div class="row mt20" style="border-bottom: solid 2px #ffffff; margin-bottom:5px;"></div>

      <!--
      <div class="row">
        <div class="small-12 adv-search-form bold-font column">q Type</div>
        <div class="small-12 column">
          {{ Form::select('institution', array('1' => 'No preference', '2' => 'Lorem','3'=>'Ipsum','4'=>'Tada'), '1',array('class'=> 'styled-select')  ) }}
        </div>
      </div>
      -->

	<?php
    if (!isset($locale)) {
       $locale = null;
    }
    if (!isset($min_reading)) {
       $min_reading = null;
    }
    if (!isset($max_reading)) {
       $max_reading = null;
    }
    if (!isset($min_sat_math)) {
       $min_sat_math = null;
    }
    if (!isset($max_sat_math)) {
       $max_sat_math = null;
    }
    if (!isset($tuition_max_val)) {
       $tuition_max_val = null;
    }
    if (!isset($religious_affiliation)) {
       $religious_affiliation = null;
    }
    if (!isset($campus_housing)) {
       $campus_housing = null;
    }
    if (!isset($min_act_composite)) {
       $min_act_composite = null;
    }
    if (!isset($max_act_composite)) {
       $max_act_composite = null;
    }
    if (!isset($page)) {
       $page = null;
    }
    if((isset($querystring['campus_housing'])) || ($locale!='') || ($min_reading!='') || ($max_reading!='') || ($min_sat_math!='') || ($max_sat_math!='') ||
    ($religious_affiliation!='') || ($tuition_max_val<20000) || ($enrollment_min_val>0) || ($applicants_min_val>0))

    {$dispaly='d-block'; $collapsfilterclass='run';} else {$dispaly='d-none';$collapsfilterclass='';}
    ?>

      <div id="filter-toggle-btn" class="row columns adv-search-form clr-orange curs-pointer bold-font <?php echo $collapsfilterclass?>">more filter options</div>


   		 <div class="filter-toggle column" style="display: none;">
            <!--<div class="row">
              <div class="small-12 adv-search-form bold-font column">Source</div>
              <div class="small-12 column"> {{ Form::select('institution', array('1' => 'No preference', '2' => 'Lorem','3'=>'Ipsum','4'=>'Tada'), '1',array('class'=> 'styled-select') ) }} </div>
            </div>-->

          <div class="small-12 adv-search-form bold-font column">
            Housing? {{ Form::checkbox('campus_housing', 1 ,$campus_housing)}}
          </div>

            <div class="row">
            <div class="small-12 adv-search-form bold-font">Campus Setting</div>
            <div class="small-12">
                {{ Form::select('locale',array('&nbsp;'),'',array('class'=> 'styled-select','id'=>'locale-select-box') )}}
            </div>
            </div>


           <div class="row">
              <div class="small-12  adv-search-form bold-font">Maximum Tuition &amp; Fees</div>
              <div class="small-12 mt10">
                <div class="slider-range-min" id="slider-range-1"></div>
              </div>
              <div class="small-12 mt5 text-center" >
             	<input type="text" id="tuition_range" readonly class="range-txt">
              <input type="hidden" name="tuition_max_val" id="tuition_max_val" class="range-txt" value="">

              </div>
          </div>



            <div class="row">
              <div class="small-12 adv-search-form bold-font">Undergraduate Enrollment</div>
              <div class="small-12 mt10"><div class="slider-range" id="slider-range-2" style="width: 98%;"></div></div>
              <div class="small-12 mt5 text-center">
              	<input type="text" id="enrollment_range" readonly class="range-txt">
                <input type="hidden" name="enrollment_min_val" id="enrollment_min_val"  class="range-txt" value="">
                <input type="hidden" name="enrollment_max_val" id="enrollment_max_val" class="range-txt" value="">
              </div>
            </div>
            <div class="row">
              <div class="small-12 adv-search-form bold-font">Acceptance Rate</div>
              <div class="small-12 mt10"><div class="slider-range" id="slider-range-3" style="width: 98%;"></div></div>
              <div class="small-12 mt5 text-center">
              	 <input type="text" id="applicants_range" readonly class="range-txt">
                 <input type="hidden" name="applicants_min_val" id="applicants_min_val" class="range-txt" value="">
                 <input type="hidden" name="applicants_max_val" id="applicants_max_val" class="range-txt" value="">
              </div>
            </div>

            <div class="row">
              <div class="small-12 columns adv-search-form bold-font">Test Scores 25% Percentile</div>
            </div>

            <div class="row">
              <div class="small-5 columns text-center adv-search-form bold-font">
              	 SAT Critical Reading
              </div>
              <div class='small-7 columns text-center mt20 no-padding'>
                    <div class="small-12">
                        <div class="small-5 columns mr5">
                        {{ Form::text('min_reading', $min_reading, array('placeholder' => 'Min','class'=>'form-min-btn','onkeypress'=>'return checkInteger(event);')) }}
                        </div>
                        <div class="small-5 columns mr10">
                        {{ Form::text('max_reading', $max_reading, array('placeholder' => 'Max','class'=>'form-min-btn','onkeypress'=>'return checkInteger(event);')) }}
                        </div>
                    </div>
              </div>
            </div>


            <div class="row">
              <div class="small-5 columns text-center adv-search-form bold-font">
              	 SAT Math
              </div>
              <div class='small-7 columns text-center mt20 no-padding'>
                    <div class="small-12">
                        <div class="small-5 columns mr5">
                        {{ Form::text('min_sat_math', $min_sat_math, array('placeholder' => 'Min','class'=>'form-min-btn','onkeypress'=>'return checkInteger(event);')) }}
                        </div>
                        <div class="small-5 columns mr10">
                        {{ Form::text('max_sat_math', $max_sat_math, array('placeholder' => 'Max','class'=>'form-min-btn','onkeypress'=>'return checkInteger(event);')) }}
                        </div>
                    </div>
              </div>
            </div>


             <div class="row">
              <div class="small-5 columns text-center adv-search-form bold-font">
              	 ACT Composite
              </div>
              <div class='small-7 columns text-center mt20 no-padding'>
                    <div class="small-12">
                        <div class="small-5 columns mr5">
                        {{ Form::text('min_act_composite', $min_act_composite, array('placeholder' => 'Min','class'=>'form-min-btn','onkeypress'=>'return checkInteger(event);')) }}
                        </div>
                        <div class="small-5 columns mr10">
                        {{ Form::text('max_act_composite', $max_act_composite, array('placeholder' => 'Max','class'=>'form-min-btn','onkeypress'=>'return checkInteger(event);')) }}
                        </div>
                    </div>
              </div>
            </div>



            <!--<div class="row">
              <div class="small-12 column adv-search-form bold-font">Athletic Teams</div>
              <div class="small-12 column">
                {{ Form::select('institution', array('1' => 'No preference', '2' => 'Lorem','3'=>'Ipsum','4'=>'Tada'), '1',array('class'=> 'styled-select') ) }}
              </div>
            </div>-->
            <!--<div class="row">
              <div class="small-12 column adv-search-form bold-font">Extended Learning Opportunities</div>
              <div class="small-12 column">
                {{ Form::select('institution', array('1' => 'No preference', '2' => 'Lorem','3'=>'Ipsum','4'=>'Tada'), '1',array('class'=> 'styled-select') ) }}
              </div>
            </div>-->
            <div class="row">
              <div class="small-12 adv-search-form bold-font">Religious Affiliation</div>
              <div class="small-12 ">
               <?php /*?> {{ Form::select('religious_affiliation',array(), $religious_affiliation_val ,array('class'=> 'styled-select','id'=>'religious-select-box') ) }}<?php */?>
                {{ Form::select('religious_affiliation',array('&nbsp;'),'',array('class'=> 'styled-select','id'=>'religious-select-box') )}}
              </div>
            </div>
           <!-- <div class="row">
              <div class="large-12 column adv-search-form bold-font">Specialized Mission</div>
              <div class="large-12 column"> {{ Form::select('institution', array('1' => 'No preference', '2' => 'Lorem','3'=>'Ipsum','4'=>'Tada'), '1',array('class'=> 'styled-select') ) }} </div>
            </div>-->
        </div>

      <!--  1 -->
      <div class="row pt20">
        <div class="small-6 columns text-center">
        <!--    {{ Form::hidden('term', $term) }}-->
         <!-- {{ Form::hidden('page', $page) }}-->

        @if ($type === 'state')
          {{ Form::hidden('type', 'college') }}
        @else
          {{ Form::hidden('type', $type) }}
        @endif
          <?php $tterm = isset($term) ? $term : ''; ?>
          {{ Form::hidden('term', $tterm) }}
          {{ Form::hidden('myMajors', false) }}

          {{ Form::reset('Clear!',array('class'=>'btn-clear','onclick'=>'formReset("advancesearchform")'))}}
        </div>
        <div class="small-6 columns text-center">
          <button id="collegeSearchLeft" class="btn-search medium-12 large-12 small-12 columns">Search</button>
        </div>
      </div>
      {{Form::close()}}

      @if($currentPage=='college')
    <!--  <div class="searchOverlay">
        <p class="overlayText">Advanced Search Coming Soon!</p>
      </div>-->
      @endif
    </div>
</div>
