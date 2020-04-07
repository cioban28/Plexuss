@extends('private.ranking.master')

@section('sidebar')    
    @include('public.includes.publicrightpanel')
@stop

@section('content')
    @include('public.includes.publicheader')   
    <div class="row collapse">
    	<div class="small-1 columns college-gpa-left"><img src="/images/profile/view-college-score.png" /></div>
        <div class="small-11 columns college-gpa-right">
        	<div class="row">
            	<div class="small-5 columns" style="border-right: solid 2px #ffffff;line-height:40px;margin-top:10px;margin-bottom:10px;">
                	<div class="row">
                    	<div class="small-8 columns white-18-bold">College GPA</div>
                        <div class="small-4 columns white-35-bold">4.0</div>
                    </div>
                </div>
                <div class="small-7 columns" style="line-height:25px;margin-top:5px;margin-bottom:5px;">
                	<div class="row">
                    	<div class="small-4 columns white-16-bold">Major GPA</div>
                        <div class="small-1 columns white-16-bold">4.0</div>
                        <div class="small-7 columns white-16-bold">Graphic Design</div>
                    </div>
                    <div class="row">
                    	<div class="small-4 columns white-16-bold">Minor GPA</div>
                        <div class="small-1 columns white-16-bold">4.0</div>
                        <div class="small-7 columns white-16-bold">Web Development</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br />
    <div class="row" style="margin:0 -15px;">
        <div class="small-12 medium-4 large-4 columns" style="padding-bottom: 15px;">
           <div class="row profile-view-box" style="background-color:#004358;">
            	<div class="small-12 columns" style="padding:15px;">
                	<div class="row collapse">
                    	<div class="small-12 columns view-box-head">GRE</div>
                    </div>
                    <div class="row collapse" style="line-height:40px;">
                    	<div class="small-9 columns view-box-subhead-left">MATH</div>
                        <div class="small-3 columns view-box-subhead-right">400</div>
                    </div>
                    <div class="row collapse" style="line-height:40px;">
                    	<div class="small-9 columns view-box-subhead-left">WRITING</div>
                        <div class="small-3 columns view-box-subhead-right">400</div>
                    </div>
                    <div class="row collapse" style="line-height:40px;">
                    	<div class="small-9 columns view-box-subhead-left">CRITICAL READING</div>
                        <div class="small-3 columns view-box-subhead-right">400</div>
                    </div>
                    <div class="row collapse">
                    	<div class="small-12 columns view-box-mid-border">&nbsp;</div>
                    </div>
                    <div class="row collapse" style="line-height:40px;">
                    	<div class="small-8 columns view-box-subhead-left">TOTAL</div>
                        <div class="small-4 columns view-box-subhead-right-total">400</div>
                    </div>
                </div>
           </div>
        </div>
        <div class="small-12 medium-4 large-4 columns" style="padding-bottom: 15px;">
        	<div class="row profile-view-box" style="background-color:#04a6ae;">
            	<div class="small-12 columns" style="padding:15px;">
                	<div class="row collapse">
                    	<div class="small-12 columns view-box-head">LSAT</div>
                    </div>
                    <div class="row collapse" style="line-height:40px;">
                    	<div class="small-9 columns view-box-subhead-left">MATH</div>
                        <div class="small-3 columns view-box-subhead-right">400</div>
                    </div>
                    <div class="row collapse" style="line-height:40px;">
                    	<div class="small-9 columns view-box-subhead-left">WRITING</div>
                        <div class="small-3 columns view-box-subhead-right">400</div>
                    </div>
                    <div class="row collapse" style="line-height:40px;">
                    	<div class="small-9 columns view-box-subhead-left">CRITICAL READING</div>
                        <div class="small-3 columns view-box-subhead-right">400</div>
                    </div>
                    <div class="row collapse">
                    	<div class="small-12 columns view-box-mid-border">&nbsp;</div>
                    </div>
                    <div class="row collapse" style="line-height:40px;">
                    	<div class="small-8 columns view-box-subhead-left">TOTAL</div>
                        <div class="small-4 columns view-box-subhead-right-total">400</div>
                    </div>
                </div>
           </div>
        </div>
        <div class="small-4 columns">&nbsp;</div>
    </div>

    <div class="row collapse">
    	<div class="small-1 columns hs-gpa-left"><img src="/images/profile/view_hs-score.png" /></div>
        <div class="small-11 columns hs-gpa-right">
        	<div class="row">
            	<div class="small-6 columns" style="border-right: solid 2px #ffffff;line-height:40px;margin-top:10px;margin-bottom:10px;">
                	<div class="row">
                    	<div class="small-8 columns white-18-bold">High School GPA</div>
                        <div class="small-4 columns white-35-bold">4.0</div>
                    </div>
                </div>
                <div class="small-6 columns" style="line-height:40px;margin-top:10px;margin-bottom:10px;">
                	<div class="row">
                    	<div class="small-8 columns white-18-bold">Weighted GPA</div>
                        <div class="small-4 columns white-35-bold">4.0</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br />
    <div class="row" style="margin:0 -15px;">
        <div class="small-12 medium-4 large-4 columns" style="padding-bottom: 15px;">
            <div class="row profile-view-box" style="background-color:#168f3a;">
            	<div class="small-12 columns" style="padding:15px;">
                	<div class="row collapse">
                    	<div class="small-12 columns view-box-head">PSAT</div>
                    </div>
                    <div class="row collapse" style="line-height:40px;">
                    	<div class="small-9 columns view-box-subhead-left">MATH</div>
                        <div class="small-3 columns view-box-subhead-right">400</div>
                    </div>
                    <div class="row collapse" style="line-height:40px;">
                    	<div class="small-9 columns view-box-subhead-left">WRITING</div>
                        <div class="small-3 columns view-box-subhead-right">400</div>
                    </div>
                    <div class="row collapse" style="line-height:40px;">
                    	<div class="small-9 columns view-box-subhead-left">CRITICAL READING</div>
                        <div class="small-3 columns view-box-subhead-right">400</div>
                    </div>
                    <div class="row collapse">
                    	<div class="small-12 columns view-box-mid-border">&nbsp;</div>
                    </div>
                    <div class="row collapse" style="line-height:40px;">
                    	<div class="small-8 columns view-box-subhead-left">TOTAL</div>
                        <div class="small-4 columns view-box-subhead-right-total">400</div>
                    </div>
                </div>
            </div>        
        </div>
        <div class="small-12 medium-4 large-4 columns" style="padding-bottom: 15px;">
            <div class="row profile-view-box" style="background-color:#1db151;">
            	<div class="small-12 columns" style="padding:15px;">
                	<div class="row collapse">
                    	<div class="small-12 columns view-box-head">SAT</div>
                    </div>
                    <div class="row collapse" style="line-height:40px;">
                    	<div class="small-9 columns view-box-subhead-left">MATH</div>
                        <div class="small-3 columns view-box-subhead-right">400</div>
                    </div>
                    <div class="row collapse" style="line-height:40px;">
                    	<div class="small-9 columns view-box-subhead-left">WRITING</div>
                        <div class="small-3 columns view-box-subhead-right">400</div>
                    </div>
                    <div class="row collapse" style="line-height:40px;">
                    	<div class="small-9 columns view-box-subhead-left">CRITICAL READING</div>
                        <div class="small-3 columns view-box-subhead-right">400</div>
                    </div>
                    <div class="row collapse">
                    	<div class="small-12 columns view-box-mid-border">&nbsp;</div>
                    </div>
                    <div class="row collapse" style="line-height:40px;">
                    	<div class="small-8 columns view-box-subhead-left">TOTAL</div>
                        <div class="small-4 columns view-box-subhead-right-total">400</div>
                    </div>
                </div>
            </div>        
        </div>
        
        <div class="small-12 medium-4 large-4 columns" style="padding-bottom: 15px;">
            <div class="row profile-view-box" style="background-color:#a0db39;">
            	<div class="small-12 columns" style="padding:15px;">
                	<div class="row collapse">
                    	<div class="small-12 columns view-box-head">ACT</div>
                    </div>
                    <div class="row collapse" style="line-height:40px;">
                    	<div class="small-12 columns view-box-subhead-right-total" style="text-align:left;">400</div>                        
                    </div>                    
                </div>
            </div>
        </div>
    </div>
    <br />
    <br />
    <br />
    <br />
@stop
