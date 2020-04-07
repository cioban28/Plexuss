<div id="step1">
  <div class="title">Step 1 of 3 Scholarship Information</div>
  <div>
    <div class="title2">Scholarship Information</div>
    <div class="clearfix">
      <div class="left-45">
        <input class="<?php ($gdata['scholarship_name_valid'] === 0 ? 'error': '')?>" name="scholarship_name" placeholder="Scholarship Name" value="<?php echo $gdata['scholarship_name'];?>" onChange="" />
        <input class="<?php ($gdata['submission_id_valid'] === 0 ? 'error': '')?>" name="submission_id" placeholder="Scholarship Submision ID (optional)" value="<?php echo $gdata['submission_id'];?>" onChange="" />
        <input class="<?php ($gdata['website_valid'] === 0 ? 'error': '')?>" name="website" placeholder="Website" value="<?php echo $gdata['website'];?>" onChange="" />
        <input class="<?php ($gdata['amount_valid'] === 0 ? 'error': '')?>" name="amount" placeholder="Max Amount (eg 2000 or 2000.00)"  value="<?php echo $gdata['amount'];?>"/>
        <input class="<?php ($gdata['numberof'] === 0 ? 'error': '')?>" name="number" placeholder="Number of Awards (eg 10)"  value="<?php echo $gdata['numberof'];?>"/>
        <DatePicker onChange="" onChangeRaw="" class="<?php ($gdata['deadline_valid'] === 0 ? 'error': '')?>" placeholderText="Deadline (MM/DD/YYYY)"  />
        <select name="reccuring">
          <option value="">Not Recurring</option>
          <option value="1">Monthly</option>
          <option value="2">Yearly</option>
          <option value="3">Biannual</option>
        </select>
      </div>
      <div class="right-55">
        <textarea name="description" placeholder="Enter Scholarship description..."><?php echo $gdata['description'];?></textarea>
      </div>
    </div>
  </div>
  <div class="clearfix mt20">
    <div class="add-sch-form-btn" onClick="nextFun();">NEXT </div>
  </div>
</div>
<div id="step2" xstyle="display:none;">
  <div class="title">Step 2 of 3 Add Targeting</div>
  <div>
    <div class="title2">Filter the results you receive in your student recommendations</div>
    <div class="row collapse tableRow">
      <div class="column small-12 large-3 show-for-large-up">
        <div class="adv-filtering-menu-container">
          <ul class="side-nav adv-filtering-menu">
            <li data-filter-tab="location" id="location" class=""> <a onClick="loadtabData('location',<?php $gdata;?>)" class="litext">Location</a>
              <div class="change-icon hide"></div>
            </li>
            <li data-filter-tab="startDateTerm" id="startDateTerm" class=""> <a onClick="loadtabData('startDateTerm',<?php $gdata;?>)" class="litext">Start Date</a>
              <div class="change-icon hide"></div>
            </li>
            <li data-filter-tab="financial" id="financial" class=""> <a onClick="loadtabData('financial',<?php $gdata;?>)" class="litext">Financials</a>
              <div class="change-icon hide"></div>
            </li>
            <li data-filter-tab="typeofschool" id="typeofschool" class=""> <a onClick="loadtabData('typeofschool',<?php $gdata;?>)" class="litext">Type of School</a>
              <div class="change-icon hide"></div>
            </li>
            <li data-filter-tab="majorDeptDegree" id="majorDeptDegree" class=""><a onClick="loadtabData('majorDeptDegree',<?php $gdata;?>)" class="litext">Major</a>
              <div class="change-icon hide"></div>
            </li>
            <li data-filter-tab="scores" id="scores" class=""> <a onClick="loadtabData('scores',<?php $gdata;?>)" class="litext">Scores</a>
              <div class="change-icon hide"></div>
            </li>
            <li data-filter-tab="uploads" id="uploads" class=""> <a onClick="loadtabData('uploads',<?php $gdata;?>)" class="litext">Uploads</a>
              <div class="change-icon hide"></div>
            </li>
            <li data-filter-tab="demographic" id="demographic" class=""> <a onClick="loadtabData('demographic',<?php $gdata;?>)" class="litext">Demographic</a>
              <div class="change-icon hide"></div>
            </li>
            <li data-filter-tab="educationLevel" id="educationLevel" class=""> <a onClick="loadtabData('educationLevel',<?php $gdata;?>)" class="litext">Education Level</a>
              <div class="change-icon hide"></div>
            </li>
            <li data-filter-tab="militaryAffiliation" id="militaryAffiliation" class=""> <a onClick="loadtabData('militaryAffiliation',<?php $gdata;?>)" class="litext">Military Affiliation</a>
              <div class="change-icon hide"></div>
            </li>
            <li data-filter-tab="profileCompletion" id="profileCompletion" class=""> <a onClick="loadtabData('profileCompletion',<?php $gdata;?>)" class="litext">Profile Completion</a>
              <div class="change-icon hide"></div>
            </li>
          </ul>
        </div>
      </div>
      <div class="column small-12 large-9">
        <div class="adv-filtering-section-container adv-main-container">
          <div class="row filter-intro-container parentDiv" id="getResult1" data-equalizer="">
            <div class="defaultDiv" id="defaultDiv" style="display:'block'">
              <div class="column small-12 medium-4 ">
                <div class="filter-intro-step" data-equalizer-watch="">
                  <div class="text-center">1</div>
                  <div class="text-center"> <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/step-1-filter.png" /> </div>
                  <div class="text-center textdec"> You receive student recommendations daily, but you're looking for certain kinds of students </div>
                </div>
              </div>
              <div class="column small-12 medium-4">
                <div class="filter-intro-step" data-equalizer-watch="">
                  <div class="text-center">2</div>
                  <div class="text-center"> <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/step-2-filter.png" /> </div>
                  <div class="text-center textdec">Choose what you'd like to filter by and save your changes (menu on the left)</div>
                </div>
              </div>
              <div class="column small-12 medium-4">
                <div class="filter-intro-step" data-equalizer-watch="">
                  <div class="text-center">3</div>
                  <div class="text-center"> <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/step-3-filter.png" /> </div>
                  <div class="text-center textdec">Based on your filters, you will receive recommendations that may be a better fit for your school </div>
                </div>
              </div>
            </div>
            <div class="locationDiv" id="locationDiv" style="display:'none'"></div>
            <div class="startDateTermDiv" id="startDateTermDiv" style="display:'none'"></div>
            <div class="startDateTermDiv" id="financialDiv" style="display:'none'"></div>
            <div class="typeofschoolDiv" id="typeofschoolDiv" style="display:'none'"></div>
            <div class="majorDeptDegreeDiv" id="majorDeptDegreeDiv" style="display:'none'"></div>
            <div class="scoresDiv" id="scoresDiv" style="display:'none'"></div>
            <div class="uploadsDiv" id="uploadsDiv" style="display:'none'"></div>
            <div class="demographicDiv" id="demographicDiv" style="display:'none'"></div>
            <div class="educationLevelDiv" id="educationLevelDiv" style="display:'none'"></div>
            <div class="militaryAffiliationDiv" id="militaryAffiliationDiv" style="display:'none'"></div>
            <div class="profileCompletionDiv" id="profileCompletionDiv" style="display:'none'"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="clearfix mt20">
    <div class="add-sch-form-btn" onClick="nextFun('2','3')" tabIndex="0">NEXT </div>
    <div class="cancel-sch-form-btn" onClick="nextFun('2','1')">BACK</div>
  </div>
</div>
<div id="step3" xstyle="display:none;">
  <div class="title">Step 3 of 3 Provider Information</div>
  <div>
    <div className="title2">Provider Information</div>
    <select className="full" >
      <option value="">New Provider</option>
    </select>
    <div>
      <input className='full' name="provider_name" placeholder="Company Name" value=""/>
      <div className="clearfix">
        <div className="left half">
          <input className="" name="contact_fname" placeholder="Contact First Name"  value=""/>
        </div>
        <div className="right half">
          <input className="" name="contact_lname" placeholder="Contact Last Name" value=""/>
        </div>
      </div>
      <div className="clearfix">
        <div className="left half">
          <input className="" name="provider_phone" placeholder="Phone" value=""/>
        </div>
        <div className="right half">
          <input className="" name="provider_email" placeholder="Email" value=""/>
        </div>
      </div>
      <input className="" name="provider_address" placeholder="Address..." value=""/>
      <div className="clearfix">
        <div className="third left">
          <input className="" name="provider_city" placeholder="City" value=""/>
        </div>
        <div className="third left">
          <input name="provider_state" placeholder="State" value=""/>
        </div>
        <div className="third right">
          <input className="" name="provider_zip" placeholder="Zip" value=""/>
        </div>
        <select className="full" name="provider_country">
        </select>
      </div>
    </div>
  </div>
  <div class="clearfix mt20">
    <input type="submit" value="ADD" className="add-sch-form-btn" />
    <div class="cancel-sch-form-btn" onClick="nextFun('3','2')">BACK</div>
  </div>
</div>
<!--<script type="text/javascript">
function nextFun(curr,nextt){
	alert('hhh');
}
</script>-->