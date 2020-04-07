import React, { Component } from 'react'
import { connect } from 'react-redux'
import {Link} from 'react-router-dom'

class SearchForm extends Component{
  constructor(props){
    super(props)
  }

  componentDidMount(){

  }

  render(){
    return(
      <form method="GET" action="https://plexuss.com/search" acceptCharset="UTF-8" data-abide="" id="advancesearchform" className="side-bar-news radius-4" noValidate="novalidate">
        <div className="row">
          <div className="small-12 columns adv-search-heading">Advanced College Search</div>
          
          <div className="small-6 columns clear-filter text-center">
            <input type="button" value="Clear form" style={{background: "none", boxShadow: "none", border: "none", textDecoration: "underline", padding: "0"}} />                            
          </div>
        </div>
        
        <div className="row">
          <div className="small-12 adv-search-form bold-font column" style={{borderRadius: "2px"}}>Name</div>

          <div className="small-12 column">
            <input name="school_name" type="text" value="" placeholder="School Name"/>
          </div>
        </div>

        <div className="row">
          <div className="small-12 adv-search-form bold-font column">Country</div>
          
          <div className="small-12 column">
            <select className="styled-select" id="country-select-box"  name="country"><option value="">No preference</option><option value="US">United States</option><option value="CA">Canada</option><option value="AF">Afghanistan</option><option value="AL">Albania</option><option value="DZ">Algeria</option><option value="AD">Andorra</option><option value="AO">Angola</option><option value="AR">Argentina</option><option value="AM">Armenia</option><option value="AW">Aruba</option><option value="AU">Australia</option><option value="AT">Austria</option><option value="AZ">Azerbaijan</option><option value="BS">Bahamas</option><option value="BH">Bahrain</option><option value="BD">Bangladesh</option><option value="BB">Barbados</option><option value="BY">Belarus</option><option value="BE">Belgium</option><option value="BZ">Belize</option><option value="BJ">Benin</option><option value="BT">Bhutan</option><option value="BO">Bolivia</option><option value="BA">Bosnia and Herzegovina</option><option value="BW">Botswana</option><option value="BR">Brazil</option><option value="BN">Brunei Darussalam</option><option value="BG">Bulgaria</option><option value="BF">Burkina Faso</option><option value="BI">Burundi</option><option value="KH">Cambodia</option><option value="CM">Cameroon</option><option value="CV">Cape Verde</option><option value="KY">Cayman Islands</option><option value="CF">Central African Republic</option><option value="TD">Chad</option><option value="CL">Chile</option><option value="CN">China</option><option value="CO">Colombia</option><option value="KM">Comoros</option><option value="CG">Congo</option><option value="CR">Costa Rica</option><option value="HR">Croatia (Hrvatska)</option><option value="CU">Cuba</option><option value="CY">Cyprus</option><option value="CZ">Czech Republic</option><option value="DK">Denmark</option><option value="DJ">Djibouti</option><option value="DO">Dominican Republic</option><option value="TP">East Timor</option><option value="EC">Ecuador</option><option value="EG">Egypt</option><option value="SV">El Salvador</option><option value="GQ">Equatorial Guinea</option><option value="ER">Eritrea</option><option value="EE">Estonia</option><option value="ET">Ethiopia</option><option value="FO">Faroe Islands</option><option value="FJ">Fiji</option><option value="FI">Finland</option><option value="FR">France</option><option value="GA">Gabon</option><option value="GM">Gambia</option><option value="GE">Georgia</option><option value="DE">Germany</option><option value="GH">Ghana</option><option value="GR">Greece</option><option value="GD">Grenada</option><option value="GT">Guatemala</option><option value="GN">Guinea</option><option value="GW">Guinea-Bissau</option><option value="GY">Guyana</option><option value="HT">Haiti</option><option value="HN">Honduras</option><option value="HU">Hungary</option><option value="IS">Iceland</option><option value="IN">India</option><option value="ID">Indonesia</option><option value="IR">Iran (Islamic Republic of)</option><option value="IQ">Iraq</option><option value="IE">Ireland</option><option value="IL">Israel</option><option value="IT">Italy</option><option value="CI">Ivory Coast</option><option value="JM">Jamaica</option><option value="JP">Japan</option><option value="JO">Jordan</option><option value="KZ">Kazakhstan</option><option value="KE">Kenya</option><option value="KP">Korea, Democratic People's Republic of</option><option value="KR">Korea, Republic of</option><option value="KW">Kuwait</option><option value="KG">Kyrgyzstan</option><option value="LA">Lao People's Democratic Republic</option><option value="LV">Latvia</option><option value="LB">Lebanon</option><option value="LS">Lesotho</option><option value="LR">Liberia</option><option value="LY">Libyan Arab Jamahiriya</option><option value="LI">Liechtenstein</option><option value="LT">Lithuania</option><option value="LU">Luxembourg</option><option value="MK">Macedonia</option><option value="MG">Madagascar</option><option value="MW">Malawi</option><option value="MY">Malaysia</option><option value="MV">Maldives</option><option value="ML">Mali</option><option value="MT">Malta</option><option value="MR">Mauritania</option><option value="MU">Mauritius</option><option value="MX">Mexico</option><option value="MC">Monaco</option><option value="MN">Mongolia</option><option value="ME">Montenegro</option><option value="MA">Morocco</option><option value="MZ">Mozambique</option><option value="MM">Myanmar</option><option value="NA">Namibia</option><option value="NP">Nepal</option><option value="NL">Netherlands</option><option value="AN">Netherlands Antilles</option><option value="NZ">New Zealand</option><option value="NI">Nicaragua</option><option value="NE">Niger</option><option value="NG">Nigeria</option><option value="NO">Norway</option><option value="OM">Oman</option><option value="PK">Pakistan</option><option value="PA">Panama</option><option value="PG">Papua New Guinea</option><option value="PY">Paraguay</option><option value="PE">Peru</option><option value="PH">Philippines</option><option value="PL">Poland</option><option value="PT">Portugal</option><option value="QA">Qatar</option><option value="RO">Romania</option><option value="RU">Russian Federation</option><option value="RW">Rwanda</option><option value="KN">Saint Kitts and Nevis</option><option value="WS">Samoa</option><option value="SM">San Marino</option><option value="ST">Sao Tome and Principe</option><option value="SA">Saudi Arabia</option><option value="SN">Senegal</option><option value="RS">Serbia</option><option value="SC">Seychelles</option><option value="SL">Sierra Leone</option><option value="SG">Singapore</option><option value="SK">Slovakia</option><option value="SI">Slovenia</option><option value="SB">Solomon Islands</option><option value="SO">Somalia</option><option value="ZA">South Africa</option><option value="ES">Spain</option><option value="LK">Sri Lanka</option><option value="SD">Sudan</option><option value="SR">Suriname</option><option value="SZ">Swaziland</option><option value="SE">Sweden</option><option value="CH">Switzerland</option><option value="SY">Syrian Arab Republic</option><option value="TJ">Tajikistan</option><option value="TZ">Tanzania, United Republic of</option><option value="TH">Thailand</option><option value="TG">Togo</option><option value="TO">Tonga</option><option value="TT">Trinidad and Tobago</option><option value="TN">Tunisia</option><option value="TR">Turkey</option><option value="TM">Turkmenistan</option><option value="UG">Uganda</option><option value="UA">Ukraine</option><option value="AE">United Arab Emirates</option><option value="GB">United Kingdom</option><option value="UY">Uruguay</option><option value="UZ">Uzbekistan</option><option value="VA">Vatican City State</option><option value="VE">Venezuela</option><option value="VN">Vietnam</option><option value="YE">Yemen</option><option value="ZM">Zambia</option><option value="ZW">Zimbabwe</option><option value="SS">South Sudan </option><option value="PS">Palestine</option></select>
          </div>
        </div>


        <div className="row">
          <div className="small-12 adv-search-form bold-font column">State</div>
          
          <div className="small-12 column" id="state_div">
            <select className="styled-select" id="state-select-box" name="state"><option value="">No preference</option><option value="Alabama">Alabama</option><option value="Alaska">Alaska</option><option value="American Samoa">American Samoa</option><option value="Arizona">Arizona</option><option value="Arkansas">Arkansas</option><option value="British Columbia">British Columbia</option><option value="California">California</option><option value="Colorado">Colorado</option><option value="Connecticut">Connecticut</option><option value="Delaware">Delaware</option><option value="District of Columbia">District of Columbia</option><option value="Federated States of Micronesia">Federated States of Micronesia</option><option value="Florida">Florida</option><option value="Gelderland">Gelderland</option><option value="Georgia">Georgia</option><option value="Guam">Guam</option><option value="Hawaii">Hawaii</option><option value="Idaho">Idaho</option><option value="Illinois">Illinois</option><option value="Indiana">Indiana</option><option value="Iowa">Iowa</option><option value="Kansas">Kansas</option><option value="Kentucky">Kentucky</option><option value="Louisiana">Louisiana</option><option value="Maine">Maine</option><option value="Marshall Islands">Marshall Islands</option><option value="Maryland">Maryland</option><option value="Massachusetts">Massachusetts</option><option value="Michigan">Michigan</option><option value="Minnesota">Minnesota</option><option value="Mississippi">Mississippi</option><option value="Missouri">Missouri</option><option value="Montana">Montana</option><option value="Nebraska">Nebraska</option><option value="Nevada">Nevada</option><option value="New Hampshire">New Hampshire</option><option value="New Jersey">New Jersey</option><option value="New Mexico">New Mexico</option><option value="New South Wales">New South Wales</option><option value="New York">New York</option><option value="North Carolina">North Carolina</option><option value="North Dakota">North Dakota</option><option value="Northern Marianas">Northern Marianas</option><option value="Ohio">Ohio</option><option value="Oklahoma">Oklahoma</option><option value="Oregon">Oregon</option><option value="Palau">Palau</option><option value="Pennsylvania">Pennsylvania</option><option value="Puerto Rico">Puerto Rico</option><option value="Rhode Island">Rhode Island</option><option value="South Carolina">South Carolina</option><option value="South Dakota">South Dakota</option><option value="Tennessee">Tennessee</option><option value="Texas">Texas</option><option value="Utah">Utah</option><option value="Vermont">Vermont</option><option value="Virgin Islands">Virgin Islands</option><option value="Virginia">Virginia</option><option value="Washington">Washington</option><option value="West Virginia">West Virginia</option><option value="Western Australia">Western Australia</option><option value="Wisconsin">Wisconsin</option><option value="Wyoming">Wyoming</option></select>
          </div>
        </div>


        <div className="row">
          <div className="small-12 adv-search-form bold-font column">City</div>
          <div className="small-12  column" id="city_div">
            <select className="styled-select" id="city-select-box" name="city"><option value="" >No preference</option></select>
          </div>
        </div>

        <div className="row">
          <div className="column small-5 adv-search-form bold-font">
            Zip Code
          </div>
          <div id="miles_range" className="column small-7 adv-search-form bold-font d-none">Within<br/>0-0 miles</div>
        </div>


        <div className="row">
          <div className="small-12 columns">
            <div className="small-12 columns no-padding">
              <input placeholder="Zip Code" className="advansed-search-txt" id="zipcode-search-txt" name="zipcode" type="text"/>
            </div>
              
            <div className="small-6 columns no-padding">
              <div className="slider-range mt5 d-none ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all" id="slider-range-0" style={{marginLeft: "-2px"}}><div className="ui-slider-range ui-widget-header ui-corner-all" style={{left: "0%", width: "0%"}}></div><span className="ui-slider-handle ui-state-default ui-corner-all" tabIndex="0" style={{left: "0%"}}></span><span className="ui-slider-handle ui-state-default ui-corner-all" tabIndex="0" style={{left: "0%"}}></span></div>
                <div className="small-12 mt5 text-center">
                  <input type="hidden" name="miles_range_min_val" id="miles_range_min_val" className="range-txt" disabled="disabled" value="0"/>
                  <input type="hidden" name="miles_range_max_val" id="miles_range_max_val" className="range-txt" disabled="disabled" value="0"/>
              </div>
            </div>
          </div>
        </div>

        <div className="row" style={{borderBottom: "solid 2px #ffffff", marginBottom: "5px"}}>
          <div className="small-12 adv-search-form bold-font column"> Degree Type</div>

          <div className="small-12 column">
            <select className="styled-select" name="degree"><option value="" >Select Degree Type</option><option value="bachelors_degree">Bachelors Degree</option><option value="masters_degree">Masters Degree</option><option value="post_masters_degree">Post Masters Degree</option><option value="doctors_degree_research">Doctors Degree Research</option><option value="doctors_degree_professional">Doctors Degree Professional</option></select>
          </div>
        </div>

        <div className="row">
          <div className="small-12 adv-search-form bold-font column">Department</div>

          <div className="small-12 column">
            <select value="" name="department" className="styled-select dept-select-box">
              <option value="" disabled="disabled" >Select a Department...</option>
              <option value="study-agriculture">Agriculture &amp; Related Sciences</option>
              <option value="study-trades">Trades &amp; Applied Sciences</option>
              <option value="study-arts">Arts, Design &amp; Architecture</option>
              <option value="study-business">Business Management &amp; Marketing</option>
              <option value="study-computer-science-it">Computer Science &amp; Information Technology</option>
              <option value="study-education">Education</option>
              <option value="study-engineering">Engineering</option>
              <option value="study-environmental-studies">Environmental Studies &amp; Natural Resources</option>
              <option value="study-parks-recreation-leisure-fitness-studies">Parks, Recreation, Leisure &amp; Fitness Studies</option>
              <option value="study-humanities">Liberal Arts &amp; Humanities</option>
              <option value="study-communications">Communication, Journalism &amp; Related Programs</option>
              <option value="study-legal-studies">Legal Professions &amp; Studies</option>
              <option value="study-health-professions">Medicine &amp; Health Professions</option>
              <option value="study-natural-sciences">Natural Sciences</option>
              <option value="study-social-sciences">Social Sciences</option>
              <option value="study-mathematics">Mathematics</option>
              <option value="study-biology">Biology</option>
            </select>
          </div>
        </div>

        <div className="majors-select-container row  hide ">
          <div className="small-12 adv-search-form bold-font column">Major  <span><div className="sm-wh-loader hide"></div></span> </div>

          <div className="small-12 column">
            <select className="styled-select adv-c-s-majors-select" name="imajor" value="">
              <option value="">Select Major...</option>
            </select>
          </div>
        </div>

        <div className="row mt20" style={{borderBottom: "solid 2px #ffffff", marginBttom: "5px"}}></div>

        <div id="filter-toggle-btn" className="row columns adv-search-form clr-orange curs-pointer bold-font run">more filter options</div>

        <div className="filter-toggle column" style={{display: "none"}}>


          <div className="small-12 adv-search-form bold-font column">
            Housing? <input name="campus_housing" type="checkbox" value="1"/>
          </div>

          <div className="row">
            <div className="small-12 adv-search-form bold-font">Campus Setting</div>

            <div className="small-12">
              <select className="styled-select" id="locale-select-box" name="locale"><option value="">No preference</option><option value=""></option><option value="{Not available}">Not available</option><option value="City: Large">City: Large</option><option value="City: Midsize">City: Midsize</option><option value="City: Small">City: Small</option><option value="Rural: Distant">Rural: Distant</option><option value="Rural: Fringe">Rural: Fringe</option><option value="Rural: Remote">Rural: Remote</option><option value="Suburb: Large">Suburb: Large</option><option value="Suburb: Midsize">Suburb: Midsize</option><option value="Suburb: Small">Suburb: Small</option><option value="Town: Distant">Town: Distant</option><option value="Town: Fringe">Town: Fringe</option><option value="Town: Remote">Town: Remote</option></select>
            </div>
          </div>


          <div className="row">
            <div className="small-12  adv-search-form bold-font">Maximum Tuition &amp; Fees</div>
            <div className="small-12 mt10">
              <div className="slider-range-min ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all" id="slider-range-1"><div className="ui-slider-range ui-widget-header ui-corner-all ui-slider-range-min" style={{width: "0%"}}></div><span className="ui-slider-handle ui-state-default ui-corner-all" tabIndex="0" style={{left: "0%"}}></span></div>
            </div>
            
            <div className="small-12 mt5 text-center">
              <input type="text" id="tuition_range" readOnly="" className="range-txt"/>
              <input type="hidden" name="tuition_max_val" id="tuition_max_val" className="range-txt" value="0"/>
            </div>
          </div>



          <div className="row">
            <div className="small-12 adv-search-form bold-font">Undergraduate Enrollment</div>
            <div className="small-12 mt10"><div className="slider-range ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all" id="slider-range-2" style={{width: "98%"}}><div className="ui-slider-range ui-widget-header ui-corner-all" style={{left: "0%", width: "0%"}}></div><span className="ui-slider-handle ui-state-default ui-corner-all" tabIndex="0" style={{left: "0%"}}></span><span className="ui-slider-handle ui-state-default ui-corner-all" tabIndex="0" style={{left: "0%"}}></span></div></div>
            <div className="small-12 mt5 text-center">
              <input type="text" id="enrollment_range" readOnly="" className="range-txt"/>
              <input type="hidden" name="enrollment_min_val" id="enrollment_min_val" className="range-txt" value="0"/>
              <input type="hidden" name="enrollment_max_val" id="enrollment_max_val" className="range-txt" value="0"/>
            </div>
          </div>
        
          <div className="row">
            <div className="small-12 adv-search-form bold-font">Acceptance Rate</div>
            <div className="small-12 mt10"><div className="slider-range ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all" id="slider-range-3" style={{width: "98%"}}><div className="ui-slider-range ui-widget-header ui-corner-all" style={{left: "0%", width: "0%"}}></div><span className="ui-slider-handle ui-state-default ui-corner-all" tabIndex="0" style={{left: "0%"}}></span><span className="ui-slider-handle ui-state-default ui-corner-all" tabIndex="0" style={{left: "0%"}}></span></div></div>
            <div className="small-12 mt5 text-center">
              <input type="text" id="applicants_range" readOnly="" className="range-txt"/>
              <input type="hidden" name="applicants_min_val" id="applicants_min_val" className="range-txt" value="0"/>
              <input type="hidden" name="applicants_max_val" id="applicants_max_val" className="range-txt" value="0"/>
            </div>
          </div>

          <div className="row">
            <div className="small-12 columns adv-search-form bold-font">Test Scores 25% Percentile</div>
          </div>

          <div className="row">
            <div className="small-5 columns text-center adv-search-form bold-font">
              SAT Critical Reading
            </div>
            <div className="small-7 columns text-center mt20 no-padding">
              <div className="small-12">
                <div className="small-5 columns mr5">
                  <input placeholder="Min" className="form-min-btn"  name="min_reading" type="text"/>
                </div>
                <div className="small-5 columns mr10">
                  <input placeholder="Max" className="form-min-btn"  name="max_reading" type="text"/>
                </div>
              </div>
            </div>
          </div>


          <div className="row">
            <div className="small-5 columns text-center adv-search-form bold-font">
              SAT Math
            </div>
            <div className="small-7 columns text-center mt20 no-padding">
              <div className="small-12">
                <div className="small-5 columns mr5">
                  <input placeholder="Min" className="form-min-btn"  name="min_sat_math" type="text"/>
                </div>
                <div className="small-5 columns mr10">
                  <input placeholder="Max" className="form-min-btn"  name="max_sat_math" type="text"/>
                </div>
              </div>
            </div>
          </div>


          <div className="row">
            <div className="small-5 columns text-center adv-search-form bold-font">
              ACT Composite
            </div>
            <div className="small-7 columns text-center mt20 no-padding">
              <div className="small-12">
                <div className="small-5 columns mr5">
                  <input placeholder="Min" className="form-min-btn"  name="min_act_composite" type="text"/>
                </div>
                <div className="small-5 columns mr10">
                  <input placeholder="Max" className="form-min-btn"  name="max_act_composite" type="text"/>
                </div>
              </div>
            </div>
          </div>



          <div className="row">
            <div className="small-12 adv-search-form bold-font">Religious Affiliation</div>
            
            <div className="small-12 ">
              <select className="styled-select" id="religious-select-box" name="religious_affiliation"><option value="">No preference</option><option value=""></option><option value=""></option><option value="African Methodist Episcopal">African Methodist Episcopal</option><option value="African Methodist Episcopal Zion Church">African Methodist Episcopal Zion Church</option><option value="American Baptist">American Baptist</option><option value="American Evangelical Lutheran Church">American Evangelical Lutheran Church</option><option value="Assemblies of God Church">Assemblies of God Church</option><option value="Baptist">Baptist</option><option value="Brethren Church">Brethren Church</option><option value="Christ and Missionary Alliance Church">Christ and Missionary Alliance Church</option><option value="Christian Church (Disciples of Christ)">Christian Church (Disciples of Christ)</option><option value="Christian Churches and Churches of Christ">Christian Churches and Churches of Christ</option><option value="Christian Methodist Episcopal">Christian Methodist Episcopal</option><option value="Christian Reformed Church">Christian Reformed Church</option><option value="Church of Brethren">Church of Brethren</option><option value="Church of God">Church of God</option><option value="Church of the Nazarene">Church of the Nazarene</option><option value="Churches of Christ">Churches of Christ</option><option value="Cumberland Presbyterian">Cumberland Presbyterian</option><option value="Episcopal Church, Reformed">Episcopal Church, Reformed</option><option value="Evangelical Christian">Evangelical Christian</option><option value="Evangelical Congregational Church">Evangelical Congregational Church</option><option value="Evangelical Covenant Church of America">Evangelical Covenant Church of America</option><option value="Evangelical Free Church of America">Evangelical Free Church of America</option><option value="Evangelical Lutheran Church">Evangelical Lutheran Church</option><option value="Free Methodist">Free Methodist</option><option value="Free Will Baptist Church">Free Will Baptist Church</option><option value="Friends">Friends</option><option value="General Baptist">General Baptist</option><option value="Greek Orthodox">Greek Orthodox</option><option value="Interdenominational">Interdenominational</option><option value="International United Pentecostal Church">International United Pentecostal Church</option><option value="Jewish">Jewish</option><option value="Latter Day Saints (Mormon Church)">Latter Day Saints (Mormon Church)</option><option value="Lutheran Church - Missouri Synod">Lutheran Church - Missouri Synod</option><option value="Lutheran Church in America">Lutheran Church in America</option><option value="Mennonite Brethren Church">Mennonite Brethren Church</option><option value="Mennonite Church">Mennonite Church</option><option value="Missionary Church Inc">Missionary Church Inc</option><option value="Moravian Church">Moravian Church</option><option value="Multiple Protestant Denomination">Multiple Protestant Denomination</option><option value="North American Baptist">North American Baptist</option><option value="Not applicable">Not applicable</option><option value="Original Free Will Baptist">Original Free Will Baptist</option><option value="Other (none of the above)">Other (none of the above)</option><option value="Other Protestant">Other Protestant</option><option value="Pentecostal Holiness Church">Pentecostal Holiness Church</option><option value="Presbyterian">Presbyterian</option><option value="Presbyterian Church (USA)">Presbyterian Church (USA)</option><option value="Protestant Episcopal">Protestant Episcopal</option><option value="Protestant, not specified">Protestant, not specified</option><option value="Reformed Church in America">Reformed Church in America</option><option value="Reformed Presbyterian Church">Reformed Presbyterian Church</option><option value="Roman Catholic">Roman Catholic</option><option value="Russian Orthodox">Russian Orthodox</option><option value="Seventh Day Adventists">Seventh Day Adventists</option><option value="Southern Baptist">Southern Baptist</option><option value="The Presbyterian Church in America">The Presbyterian Church in America</option><option value="Undenominational">Undenominational</option><option value="Unitarian Universalist">Unitarian Universalist</option><option value="United Brethren Church">United Brethren Church</option><option value="United Church of Christ">United Church of Christ</option><option value="United Methodist">United Methodist</option><option value="Wesleyan">Wesleyan</option><option value="Wisconsin Evangelical Lutheran Synod">Wisconsin Evangelical Lutheran Synod</option></select>
            </div>
          </div>
        </div>

        <div className="row pt20">
          <div className="small-6 columns text-center">
            <input name="type" type="hidden" value="college"/>

            <input name="term" type="hidden" value=""/>

            <input name="myMajors" type="hidden" value=""/>

            <input className="btn-clear-form" type="reset" value="Clear!"/>
          </div>
          
          <div className="small-6 columns text-center">
            <button id="collegeSearchLeft" className="btn-search-form medium-12 large-12 small-12 columns">Search</button>
          </div>
        </div>
      </form>
    )
  }
}

export default SearchForm;