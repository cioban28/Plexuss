@extends('checkout.master-checkout')

@section('content')
<div class="payment-background">
  <div class="payment-method-block">
    <h2>Confirm Plan</h2>
    <div class="payment-box confirm-plan-box">
      <form class="payment-creditcard-form">
        <div class="row">
          <div class="large-12 columns product-price">
            <strong>Product</strong>
            <span>Price</span>
          </div>
          <div class="large-12 columns plexuspremium-imageara">
            <div class="large-6 small-12 columns">
              <span>Plexuss Premium</span>
              <img class="" src="/images/checkout/Checkout-premium member logo diploma.png">
            </div>
            <div class="large-6 small-8 columns text-right mob-none">
              <span>$499</span>
            </div>
          </div>
          <div class="large-12 columns total-area">
            <strong>Total</strong>
            <span>$499</span>
          </div>
        </div>
      </form>
      <div class="refund-block">
        <img src="/images/checkout/Plexuss premium badge gold.png" class="stripe-img">
        <span>Guaranteed I-20 Form within 12 Months or Receive a Full Refund</span>
      </div>
    </div>
    <h2>Chose your payment method</h2>
    <div class="payment-box">
      <div class="cell checkout stripe-wrapper">
          <div class="error d-none" role="alert" id="error-box">
            <img src="/images/exclamation-circle-solid.svg" class="exclamation-mark">
            <h1 class="error-header">Please fix or enter the following:</h1>
            <span class="message"></span>
          </div>
           <div class="accordion-radio1 accordion-radio creditCard-radio  in-active">
            <div class="payment-boader-bottom">
              <div class="payment-textbox">
                <div class="radio-box">
                  <input type="radio" id="creditCard" name="radio-group">
                  <label for="creditCard">Credit or Debit Card</label>
                </div>
                <div class="payment-textarea">
                  <p>Visa, Mastercard, Amex</p>
                </div>
              </div>
              <div id="card-img-holder" class="payment-image-icon img-opacity">
                <ul class="card-list">
                  <li>
                    <img class="active-image" src="/images/checkout/Visa.png">
                    <img class="in-active-image" src="/images/checkout/gray-Visa.png">
                  </li>
                  <li>
                    <img class="active-image" src="/images/checkout/Mastercard.png">
                    <img class="in-active-image" src="/images/checkout/gray-Mastercard.png">
                  </li>
                  <li>
                    <img class="active-image" src="/images/checkout/amex.png">
                    <img class="in-active-image" src="/images/checkout/gray-amex.png">
                  </li>
                </ul>
              </div>
            </div>
            <form>
            <div class="hiders d-none mt30" >
              <input type="hidden" name="token" />
              <div data-locale-reversible>
                <div class="row">
                  <div class="field">
                    <div id="stripe-wrapper-card-number" class="input empty"></div>
                    <label for="stripe-wrapper-card-number" data-tid="elements_checkouts.form.card_number_label">CREDIT CARD NUMBER</label>
                  </div>
                </div>
                <div class="custom-row">
                  <div class="field large-6 custom-col columns">
                    <div id="stripe-wrapper-card-expiry" class="input empty"></div>
                    <label for="stripe-wrapper-card-expiry" data-tid="elements_checkouts.form.card_expiry_label">EXPIRATION DATE</label>
                  </div>
                  <div class="field large-6 custom-col columns">
                    <div id="stripe-wrapper-card-cvc" class="input empty"></div>
                    <label for="stripe-wrapper-card-cvc" data-tid="elements_checkouts.form.card_cvc_label">CVC CODE</label>
                  </div>
                </div>
                <div class="row" data-locale-reversible>
                  <div class="field">
                    <input id="stripe-wrapper-name" data-tid="elements_checkouts.form.name_placeholder" class="input empty" type="text" placeholder="John Doe" required="" autocomplete="address-level1">
                    <label for="stripe-wrapper-name" data-tid="elements_checkouts.form.name_label">NAME ON CARD</label>
                  </div>
                </div>
                <div class="custom-row" data-locale-reversible>
                  <div class="field large-6 custom-col columns">
                    <select id="combobox">
                      <option value=""></option>
                      <option value="AF">Afghanistan</option>
                      <option value="AX">Åland Islands</option>
                      <option value="AL">Albania</option>
                      <option value="DZ">Algeria</option>
                      <option value="AS">American Samoa</option>
                      <option value="AD">Andorra</option>
                      <option value="AO">Angola</option>
                      <option value="AI">Anguilla</option>
                      <option value="AQ">Antarctica</option>
                      <option value="AG">Antigua and Barbuda</option>
                      <option value="AR">Argentina</option>
                      <option value="AM">Armenia</option>
                      <option value="AW">Aruba</option>
                      <option value="AU">Australia</option>
                      <option value="AT">Austria</option>
                      <option value="AZ">Azerbaijan</option>
                      <option value="BS">Bahamas</option>
                      <option value="BH">Bahrain</option>
                      <option value="BD">Bangladesh</option>
                      <option value="BB">Barbados</option>
                      <option value="BY">Belarus</option>
                      <option value="BE">Belgium</option>
                      <option value="BZ">Belize</option>
                      <option value="BJ">Benin</option>
                      <option value="BM">Bermuda</option>
                      <option value="BT">Bhutan</option>
                      <option value="BO">Bolivia, Plurinational State of</option>
                      <option value="BQ">Bonaire, Sint Eustatius and Saba</option>
                      <option value="BA">Bosnia and Herzegovina</option>
                      <option value="BW">Botswana</option>
                      <option value="BV">Bouvet Island</option>
                      <option value="BR">Brazil</option>
                      <option value="IO">British Indian Ocean Territory</option>
                      <option value="BN">Brunei Darussalam</option>
                      <option value="BG">Bulgaria</option>
                      <option value="BF">Burkina Faso</option>
                      <option value="BI">Burundi</option>
                      <option value="KH">Cambodia</option>
                      <option value="CM">Cameroon</option>
                      <option value="CA">Canada</option>
                      <option value="CV">Cape Verde</option>
                      <option value="KY">Cayman Islands</option>
                      <option value="CF">Central African Republic</option>
                      <option value="TD">Chad</option>
                      <option value="CL">Chile</option>
                      <option value="CN">China</option>
                      <option value="CX">Christmas Island</option>
                      <option value="CC">Cocos (Keeling) Islands</option>
                      <option value="CO">Colombia</option>
                      <option value="KM">Comoros</option>
                      <option value="CG">Congo</option>
                      <option value="CD">Congo, the Democratic Republic of the</option>
                      <option value="CK">Cook Islands</option>
                      <option value="CR">Costa Rica</option>
                      <option value="CI">Côte d'Ivoire</option>
                      <option value="HR">Croatia</option>
                      <option value="CU">Cuba</option>
                      <option value="CW">Curaçao</option>
                      <option value="CY">Cyprus</option>
                      <option value="CZ">Czech Republic</option>
                      <option value="DK">Denmark</option>
                      <option value="DJ">Djibouti</option>
                      <option value="DM">Dominica</option>
                      <option value="DO">Dominican Republic</option>
                      <option value="EC">Ecuador</option>
                      <option value="EG">Egypt</option>
                      <option value="SV">El Salvador</option>
                      <option value="GQ">Equatorial Guinea</option>
                      <option value="ER">Eritrea</option>
                      <option value="EE">Estonia</option>
                      <option value="ET">Ethiopia</option>
                      <option value="FK">Falkland Islands (Malvinas)</option>
                      <option value="FO">Faroe Islands</option>
                      <option value="FJ">Fiji</option>
                      <option value="FI">Finland</option>
                      <option value="FR">France</option>
                      <option value="GF">French Guiana</option>
                      <option value="PF">French Polynesia</option>
                      <option value="TF">French Southern Territories</option>
                      <option value="GA">Gabon</option>
                      <option value="GM">Gambia</option>
                      <option value="GE">Georgia</option>
                      <option value="DE">Germany</option>
                      <option value="GH">Ghana</option>
                      <option value="GI">Gibraltar</option>
                      <option value="GR">Greece</option>
                      <option value="GL">Greenland</option>
                      <option value="GD">Grenada</option>
                      <option value="GP">Guadeloupe</option>
                      <option value="GU">Guam</option>
                      <option value="GT">Guatemala</option>
                      <option value="GG">Guernsey</option>
                      <option value="GN">Guinea</option>
                      <option value="GW">Guinea-Bissau</option>
                      <option value="GY">Guyana</option>
                      <option value="HT">Haiti</option>
                      <option value="HM">Heard Island and McDonald Islands</option>
                      <option value="VA">Holy See (Vatican City State)</option>
                      <option value="HN">Honduras</option>
                      <option value="HK">Hong Kong</option>
                      <option value="HU">Hungary</option>
                      <option value="IS">Iceland</option>
                      <option value="IN">India</option>
                      <option value="ID">Indonesia</option>
                      <option value="IR">Iran, Islamic Republic of</option>
                      <option value="IQ">Iraq</option>
                      <option value="IE">Ireland</option>
                      <option value="IM">Isle of Man</option>
                      <option value="IL">Israel</option>
                      <option value="IT">Italy</option>
                      <option value="JM">Jamaica</option>
                      <option value="JP">Japan</option>
                      <option value="JE">Jersey</option>
                      <option value="JO">Jordan</option>
                      <option value="KZ">Kazakhstan</option>
                      <option value="KE">Kenya</option>
                      <option value="KI">Kiribati</option>
                      <option value="KP">Korea, Democratic People's Republic of</option>
                      <option value="KR">Korea, Republic of</option>
                      <option value="KW">Kuwait</option>
                      <option value="KG">Kyrgyzstan</option>
                      <option value="LA">Lao People's Democratic Republic</option>
                      <option value="LV">Latvia</option>
                      <option value="LB">Lebanon</option>
                      <option value="LS">Lesotho</option>
                      <option value="LR">Liberia</option>
                      <option value="LY">Libya</option>
                      <option value="LI">Liechtenstein</option>
                      <option value="LT">Lithuania</option>
                      <option value="LU">Luxembourg</option>
                      <option value="MO">Macao</option>
                      <option value="MK">Macedonia, the former Yugoslav Republic of</option>
                      <option value="MG">Madagascar</option>
                      <option value="MW">Malawi</option>
                      <option value="MY">Malaysia</option>
                      <option value="MV">Maldives</option>
                      <option value="ML">Mali</option>
                      <option value="MT">Malta</option>
                      <option value="MH">Marshall Islands</option>
                      <option value="MQ">Martinique</option>
                      <option value="MR">Mauritania</option>
                      <option value="MU">Mauritius</option>
                      <option value="YT">Mayotte</option>
                      <option value="MX">Mexico</option>
                      <option value="FM">Micronesia, Federated States of</option>
                      <option value="MD">Moldova, Republic of</option>
                      <option value="MC">Monaco</option>
                      <option value="MN">Mongolia</option>
                      <option value="ME">Montenegro</option>
                      <option value="MS">Montserrat</option>
                      <option value="MA">Morocco</option>
                      <option value="MZ">Mozambique</option>
                      <option value="MM">Myanmar</option>
                      <option value="NA">Namibia</option>
                      <option value="NR">Nauru</option>
                      <option value="NP">Nepal</option>
                      <option value="NL">Netherlands</option>
                      <option value="NC">New Caledonia</option>
                      <option value="NZ">New Zealand</option>
                      <option value="NI">Nicaragua</option>
                      <option value="NE">Niger</option>
                      <option value="NG">Nigeria</option>
                      <option value="NU">Niue</option>
                      <option value="NF">Norfolk Island</option>
                      <option value="MP">Northern Mariana Islands</option>
                      <option value="NO">Norway</option>
                      <option value="OM">Oman</option>
                      <option value="PK">Pakistan</option>
                      <option value="PW">Palau</option>
                      <option value="PS">Palestinian Territory, Occupied</option>
                      <option value="PA">Panama</option>
                      <option value="PG">Papua New Guinea</option>
                      <option value="PY">Paraguay</option>
                      <option value="PE">Peru</option>
                      <option value="PH">Philippines</option>
                      <option value="PN">Pitcairn</option>
                      <option value="PL">Poland</option>
                      <option value="PT">Portugal</option>
                      <option value="PR">Puerto Rico</option>
                      <option value="QA">Qatar</option>
                      <option value="RE">Réunion</option>
                      <option value="RO">Romania</option>
                      <option value="RU">Russian Federation</option>
                      <option value="RW">Rwanda</option>
                      <option value="BL">Saint Barthélemy</option>
                      <option value="SH">Saint Helena, Ascension and Tristan da Cunha</option>
                      <option value="KN">Saint Kitts and Nevis</option>
                      <option value="LC">Saint Lucia</option>
                      <option value="MF">Saint Martin (French part)</option>
                      <option value="PM">Saint Pierre and Miquelon</option>
                      <option value="VC">Saint Vincent and the Grenadines</option>
                      <option value="WS">Samoa</option>
                      <option value="SM">San Marino</option>
                      <option value="ST">Sao Tome and Principe</option>
                      <option value="SA">Saudi Arabia</option>
                      <option value="SN">Senegal</option>
                      <option value="RS">Serbia</option>
                      <option value="SC">Seychelles</option>
                      <option value="SL">Sierra Leone</option>
                      <option value="SG">Singapore</option>
                      <option value="SX">Sint Maarten (Dutch part)</option>
                      <option value="SK">Slovakia</option>
                      <option value="SI">Slovenia</option>
                      <option value="SB">Solomon Islands</option>
                      <option value="SO">Somalia</option>
                      <option value="ZA">South Africa</option>
                      <option value="GS">South Georgia and the South Sandwich Islands</option>
                      <option value="SS">South Sudan</option>
                      <option value="ES">Spain</option>
                      <option value="LK">Sri Lanka</option>
                      <option value="SD">Sudan</option>
                      <option value="SR">Suriname</option>
                      <option value="SJ">Svalbard and Jan Mayen</option>
                      <option value="SZ">Swaziland</option>
                      <option value="SE">Sweden</option>
                      <option value="CH">Switzerland</option>
                      <option value="SY">Syrian Arab Republic</option>
                      <option value="TW">Taiwan, Province of China</option>
                      <option value="TJ">Tajikistan</option>
                      <option value="TZ">Tanzania, United Republic of</option>
                      <option value="TH">Thailand</option>
                      <option value="TL">Timor-Leste</option>
                      <option value="TG">Togo</option>
                      <option value="TK">Tokelau</option>
                      <option value="TO">Tonga</option>
                      <option value="TT">Trinidad and Tobago</option>
                      <option value="TN">Tunisia</option>
                      <option value="TR">Turkey</option>
                      <option value="TM">Turkmenistan</option>
                      <option value="TC">Turks and Caicos Islands</option>
                      <option value="TV">Tuvalu</option>
                      <option value="UG">Uganda</option>
                      <option value="UA">Ukraine</option>
                      <option value="AE">United Arab Emirates</option>
                      <option value="GB">United Kingdom</option>
                      <option value="US">United States</option>
                      <option value="UM">United States Minor Outlying Islands</option>
                      <option value="UY">Uruguay</option>
                      <option value="UZ">Uzbekistan</option>
                      <option value="VU">Vanuatu</option>
                      <option value="VE">Venezuela, Bolivarian Republic of</option>
                      <option value="VN">Viet Nam</option>
                      <option value="VG">Virgin Islands, British</option>
                      <option value="VI">Virgin Islands, U.S.</option>
                      <option value="WF">Wallis and Futuna</option>
                      <option value="EH">Western Sahara</option>
                      <option value="YE">Yemen</option>
                      <option value="ZM">Zambia</option>
                      <option value="ZW">Zimbabwe</option>
                    </select>

                    <!-- <input id="stripe-wrapper-country" data-tid="elements_checkouts.form.country_placeholder" class="input empty" type="text" placeholder="CA" required="" autocomplete="address-level1"> -->
                    <label for="combobox" data-tid="elements_checkouts.form.country_label">BILLING COUNTRY</label>
                  </div>
                  <div class="field large-6 custom-col columns">
                    <input id="stripe-wrapper-zip" data-tid="elements_checkouts.form.postal_code_placeholder" class="input empty" type="number" placeholder="94107" min="1" max="99999" maxlength="5" inputmode="numeric" pattern="[0-9]*" required="" autocomplete="postal-code">
                    <label for="stripe-wrapper-zip" data-tid="elements_checkouts.form.postal_code_label">BILLING POSTAL CODE</label>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
   
      </div>

      <div class="accordion-radio2 accordion-radio in-active">
        <div class="payment-boader-bottom">
          <div class="payment-textbox">
            <div class="radio-box">
              <input type="radio" id="paypal" name="radio-group">
              <label for="paypal">PayPal</label>
            </div>
            <div class="payment-textarea">
              <p>PayPal is the faster, safer way to send money, make an online payment, receive money. Account not needed.</p>
            </div>
          </div>
          <div id="paypal-img-holder" class="payment-image-icon img-opacity">
            <img class="active-image" src="/images/checkout/2000px-PayPal.svg.png">
            <img class="in-active-image" src="/images/checkout/gray-2000px-PayPal.svg.png" class="stripe-img">
          </div>
        </div>
      </div>
      <!-- <div class="accordion-radio3 accordion-radio in-active">
        <div class="payment-boader-bottom">
          <div class="payment-textbox">
             <div class="radio-box">
              <input type="radio" id="money" name="radio-group">
              <label for="money">Money Transfer</label>
            </div>
            <div class="payment-textarea">
              <p>Pay using Money Transfer through Western Union</p>
            </div>
          </div>
          <div id="money-img-holder" class="payment-image-icon img-opacity">
            <img class="western-union active-image" src="/images/checkout/western-union_82079.png">
            <img class="western-union in-active-image" src="/images/checkout/gray-western-union_82079.png">
          </div>
        </div>
      </div> -->
      <div class="accordion-radio">
        <div class="checkout-btnarea">
          <a class="button custom-btn checkout-btn">CHECK OUT</a>
          <img src="/images/checkout/stripe_secure.png" class="stripe-img">
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  var stripeKey = '';
</script>
@stop
