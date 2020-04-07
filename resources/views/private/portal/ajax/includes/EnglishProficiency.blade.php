<?php 
    //dd($data);

?>
<!--////////////// College Specific Questions modal --- English Proficiency /////////////////-->

<!-- english form beginning-->
<div class="eng-radio-container">   
        <!-- native speaker radio -->
        <input type="radio" name="englishKnowledge" id="native" value="nativeSpeaker" required="required"/>
            <label for="native" class="clearfix">
                <div class="radio-col"><span></span></div>
                <div class="radio-desc-col">
                English is my native language.
                </div>
            </label>
            <br/>
         
        <!-- some experience radio--> 
        <div class="clearfix">
        <input type="radio" name="englishKnowledge" value="someExperience" id="experience" />
            <label for="experience">
                <div class="radio-col" class="clearfix">
                    <span id="took_eng_test"></span> 
                    </div>
                <div class="radio-desc-col">
                    English is not my native language, but I have other English experience.
                </div>
            </label></div>

                <div class="eng-checkbox-container clearfix">

                    <!-- toefl-->
                    <div class="row">
                        <div class="column large-9 medium-8 small-6">
                            <input type="checkbox" name="toefl" id="toefl" value="toefl" class="score-check eng-check"/>
                            <label for="toefl" class="clearfix">  
                                <div class="check-col"><span></span></div>
                                <div class="check-desc-col">  
                                I have taken the TOEFL (Test of English as a Foreign Language)
                                </div>
                                <div class="check-desc-col-m">  
                                TOEFL
                                </div>
                            </label><br/>
                        </div>
                        <div class="column large-3 medium-4 small-6 score-cont">
                            <span class="score-title">Score</span>
                            {{ Form::text('toefl_score', 'score', array( 'id' => 'toeflScore', 'class' => 'eng-score', 'pattern'=>'toefl') )}}
                            <small class='error'>value must be between 0-120</small>
                        </div>
                    </div>

                    <!-- ielts-->
                    <div class="row">
                        <div class="column large-9 medium-8 small-6">
                            <input type="checkbox" name="ielts" id="ielts"  value="ielts" class="score-check eng-check"/>
                            <label for="ielts" class="clearfix">
                                <div class="check-col"><span></span></div>
                                <div class="check-desc-col">
                                I have taken the IELTS (International English Language Testing System)
                                </div>
                                <div class="check-desc-col-m">
                                IELTS
                                </div>
                            </label><br/>
                        </div>
                        <div class="column large-3 medium-4 small-6 score-cont">
                            <span class="score-title">Score</span>
                             {{ Form::text('ielts_score', 'score', array( 'id' => 'ieltsScore', 'class' => 'eng-score', 'pattern'=>'ielts') )}}
                            <small class='error'>value must be between 0-9 and can be a decimal</small>
                        </div>
                    </div>

                    <!-- itep-->
                    <div class="row">
                        <div class="column large-9 medium-8 small-6">
                            <input type="checkbox" name="itep" id="itep" value="itep" class="score-check eng-check"/>
                            <label for="itep" class="clearfix">
                                <div class="check-col"><span></span></div>
                                <div class="check-desc-col">
                                I have taken the ITEP (International Test of English Proficiency)
                                </div>
                                <div class="check-desc-col-m">
                                ITEP
                                </div>
                            </label><br/>
                        </div>
                        <div class="column large-3 medium-4 small-6 score-cont">
                            <span class="score-title">Score</span>
                             {{ Form::text('itep_score', 'score', array( 'id' => 'itepScore', 'class' => 'eng-score', 'pattern'=>'itep') )}}
                            <small class='error'>value must be between 0-6 and can be a decimal</small>
                        </div>
                    </div>
                        
                    <!-- pte-->
                    <div class="row">
                        <div class="column large-9 medium-8 small-6">
                            <input type="checkbox" name="pte" id="pte" value="pte" class="score-check eng-check" />
                            <label for="pte" class="clearfix">
                                <div class="check-col"><span></span></div>
                                <div class="check-desc-col">
                                I have taken the PTE (Pearson Test of English)
                                </div>
                                <div class="check-desc-col-m">
                                PTE
                                </div>
                            </label><br/>
                        </div>
                        <div class="column large-3 medium-4 small-6 score-cont">
                            <span class="score-title">Score</span>
                             {{ Form::text('pte_score', 'score', array( 'id' => 'pteScore', 'class' => 'eng-score', 'pattern'=>'pte') )}}
                            <small class='error'>value must be between 10-90</small>
                        </div>
                    </div>

                    <!-- attended English speaking institution -->
                    <div class="row">
                        <div class="column large-7 medium-7 small-12">
                            <input type="checkbox" name="institution" value="institution" id="attended" class="eng-check" />
                             <label for="attended" class="clearfix">
                                <div class="check-col"><span></span></div>
                                <div class="check-desc-col">
                                I have attended an English-speaking institution
                                </div>
                                <div class="check-desc-col-m-a">
                                I have attended an English-speaking institution
                                </div>
                            </label>
                        </div>
                        <div class="column large-5 medium-5 small-11">
                             {{ Form::text('institute_name', 'Institution', array( 'id' => 'eng_inst', 'class' => 'eng-inst', 'pattern'=>'college_name') )}}
                            <small class='error'>Invalid input. College names may only contain characters (a-z,A-Z), space characters, and hyphen (-).</small>
                        </div>
                    </div>        
            </div>

        <!-- planning to take test radio-->  
        <input type="radio" name="englishKnowledge" value="planningTest" id="plans" />
            <label for="plans" class="plans-label">
                <div class="radio-col"><span></span></div>
                <div class="radio-desc-col">
                English is not my native language, but I am planning to take one or more English exams.
                </div>
            </label>
            <br/>


</div>
