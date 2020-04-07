import React, { Component } from 'react';
import { connect } from 'react-redux'
import Loader from '../Header/loader'
import {withRouter, Link} from 'react-router-dom';
import { getStepStatuses, getStepDatas, getUserNames } from '../../api/step';
import { SpinningBubbles } from './../Header/loader/loader'
import _ from 'lodash'

var styles = {
	bad: {
		border: '1px solid firebrick',
	},
	good: {
		border: '1px solid skyblue',
  },
  good_green: {
		border: '1px solid #24b26b',
	},
	bad_code: {
		borderTopColor: 'firebrick',
		borderLeftColor: 'firebrick',
		borderBottomColor: 'firebrick',
	},
}

let currentYear = new Date().getFullYear();

class Step5 extends Component {
    constructor(props) {
        super(props)
        this.state = {
          isLoading: true,
          save_route: '/get_started/save',
          get_route: '/get_started/getDataFor/step',
          confirm_code_route: '/get_started/checkPhoneConfirmation',
          resend_code_route: '/get_started/sendPhoneConfirmation',
          new_phone_num_route: '/get_started/saveNewPhone',
          validate_with_twilio_route: '/get_started/validatePhone',
          step_num: this.props.currentPage,
          intrest: false,
          is_valid: false,
          is_sending: false,
          back_route: null,
          next_route: null,
          save_btn_classes: 'right btn submit-btn text-center',
          save_has_been_clicked: !1,
          needConfirmation: false,
          _zipValid: true,
          enableConfirmationCode: false,
          term_list: ['Fall', 'Spring', 'Summer','Winter'],
          year_list: [String(currentYear), String(currentYear+1), String(currentYear+2), String(currentYear+3), String(currentYear+4), String(currentYear+5)],
          payment_list: [{
            value: '0 - 5,000',
            label: '$0 - $5,000'
          },{
              value: '5,000 - 10,000',
              label: '$5,000 - $10,000'
          },{
              value: '10,000 - 20,000',
              label: '$10,000 - $20,000'
          },{
              value: '20,000 - 30,000',
              label: '$20,000 - $30,000'
          },{
              value: '30,000 - 50,000',
              label: '$30,000 - $50,000'
          },{
              value: '50,000',
              label: '$50,000 - more'
          }],
        }
        this._getData = this._getData.bind(this)
        this._validate = this._validate.bind(this)
        this._initData = this._initData.bind(this)
        this._enableNextIfFormIsValid = this._enableNextIfFormIsValid.bind(this)
        this._save = this._save.bind(this)
        this._checkingTheinterest = this._checkingTheinterest.bind(this)
    }

    _getData(){
        var _this = this;
        getStepDatas('5new')
        .then(() => {
          _this._initData(_this.props.step)
          getUserNames()
          .then(() => {
            _this.setState({isLoading: false})
          })
        })
    }

    _initData(data){
      var valid = false, dataForValidation = {};

      this.setState({intrest: data.intrest})
      for( var prop in data ){
          if( data.hasOwnProperty(prop) && prop !== 'txt_opt_in' && prop !== 'dialing_code' ){
              dataForValidation.id = '_'+prop;
              dataForValidation.name = prop;
              dataForValidation.value = data[prop];
              this._validate(dataForValidation);
          }

      
      }
    }

    _checkingTheinterest(){
      this.setState({
        intrest: !this.state.intrest,
      });
   }

    _validate(e){
      var newState = {}, valid = false,
          id = e.target ? e.target.id : e.id, //if e has target prop, it's coming from event, else e is an object created in _initData
          name = e.target ? e.target.getAttribute('name') : e.name,
          value = e.target ? e.target.value : e.value;

      switch( name ){
           case 'term':
             if(value) valid = true;
               break;
           case 'year':
             if(value) valid = true;
               break;
           case 'payment':
             if(value) valid = true;
               break;
            case 'phone':
              valid = true; // phone no longer required
              break;


          case 'address':
              if( /^[a-zA-Z0-9\.,#\- ]+$/.test(value) && value ) valid = true;
               break;

          case 'city':
              if( /^[a-zA-Z\.\- ]+$/.test(value) && value ) valid = true;
              break;

          case 'country':
              // if changing country from other country (or first time changing) then validate state also
              if( +value === 1 ){
                  valid = true; // country is valid if here

                  // if state is empty while country is US, make invalid
                  if( !this.state.state ){
                      this.setState({_stateValid:false, _stateValidated: true})
                  }

              }else if( value ) valid = true;

              value = +value;
              break;

          case 'term':
              // if changing country from other country (or first time changing) then validate state also
              if( +value === 1 ){
                  valid = true; // country is valid if here

                  // if state is empty while country is US, make invalid
                  if( !this.state.state ){
                      this.setState({_stateValid:false, _stateValidated: true})
                  }

              }else if( value ) valid = true;

              value = +value;
              break;

          case 'state':
              // state is only required for US students only - optional for all international
              if( this.state.country === 1 ){
                  if( value ) valid = true;

              }else valid = true;

              break;

          case 'zip':
              //if zip has a value, validate it, otherwise empty is ok b/c it's not required
              if( value ) {
                  if( /^[a-zA-Z0-9\.,\- ]+$/.test(value) ) valid = true;
                  else valid = false;
              }else{
                  valid = false;
              }

              break;

          case 'code':
              if( value && value.length === 4 && _.isNumber(+value) && (+value >= 1000 && +value <= 9999) ) valid = true;
              break;

          default: return;
      }

      let obj = {}
      obj[name] = value;
      obj[id+'Valid']=valid
      obj[id+'Validated']=true
      obj['focused']=id
      this.setState(obj, ()=>this._enableNextIfFormIsValid())

    }

    _enableNextIfFormIsValid(){
      var s = this.state, valid = false

      if( !s.needConfirmation ){
      //if( s._countryValid && s._addressValid && s._cityValid && s._stateValid && s._zipValid ) valid = true;
          if( s._termValid && s._yearValid && s._paymentValid ) valid = true;
          this.setState({enableConfirmationCode: valid})
      }else{
          if( s._confirmationCodeValid ) valid = true;
          this.setState({enableNextStep: valid})
      }
    }

    _save(e){
      e.preventDefault();

      var _this = this, s = this.state, formData = new FormData( $('form')[0] );

      if( s.enableConfirmationCode ){
          this.setState({is_sending: true});

          $.ajax({
              url: s.save_route,
              type: 'POST',
              data: formData,
              enctype: 'multipart/form-data',
              contentType: false,
              processData: false,
              headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
          }).done(function(data){
              //if an object is returned, it's an error
              if( data && _.isObject(data) && !_.isNumber(data) ){
                  _this.setState({is_sending: false, err: data.error, errMsg: data.msg});//remove loader
              }else{
                getStepStatuses('')
                .then(()=>{
                  _this._getData()
                  amplitude.getInstance().logEvent('step5_completed', {content: 'Start Date'} );

                  var txt_opt = $('#_text').is(':checked');
                  if( txt_opt ){
                      _this.setState({is_sending: false, needConfirmation: true});//remove loader
                      _this._resendCode();
                  }else{
                    _this.props.history.push(s.next_route)
                  }
                })
              }

              if( JSON.parse(sessionStorage.getItem('college_id')) ){
                  _this.setState({is_sending: !1});//remove loader
                  // $(document).trigger('saved');

              }else if( s.needConfirmation ){
                  window.location.href = s.next_route;
              }
          });
      }
    }

    componentDidMount() {
      var classes = this.state.save_btn_classes, prev, next, num, _this = this;

      // FacebooknowsGPA event tracking
      fbq('track', 'GetStarted_Step5_Grades_Page');

      //build prev step route
      num = parseInt(this.state.step_num);
      prev = num - 1;
      next = num + 1;
      this.setState({
        back_route: '/get_started/'+prev,
        next_route: '/get_started/'+next
      })

      this._getData()
    }

    render() {
      var s = this.state, _this = this;

      return (
        <div className="step_container">
          {this.state.isLoading ? ( <SpinningBubbles/> ) : (
            <span>
              <div className="row phone-container">
                <div className="column small-12">
                  <form>
                    <input type="hidden" name="step" value="5"/>
                    <div className="row">
                      <h3 className="columns small-12 phone-title">Hi {this.props.username}, you're almost finished!</h3>
                      <div className="columns small-12 phone-title">When do you plan on starting college?</div>
                      <div className="columns small-12 medium-8 row_div">
                        <select id="_term" onChange={this._validate} onFocus={this._validate} value={s.term||''} className="row-select" 
                          style={s.focused==='_term' ? (s._termValid ? styles.good : styles.bad) : (s._termValidated && !s._termValid ? styles.bad : styles.good_green)}
                          name="term">
                          <option value="" disabled="disabled">Select term</option>
                          {s.term_list.map(function(term){
                            return <option key={term} value={term}>{term}</option>
                          })}
                        </select>
                      </div>
                      <div className="columns small-12 medium-8 row_div">
                        <select id="_year" onChange={this._validate} onFocus={this._validate} value={s.year||''} className="row-select" 
                          style={s.focused==='_year' ? (s._termValid ? styles.good : styles.bad) : (s._termValidated && !s._termValid ? styles.bad : styles.good_green)}
                          name="year">
                          <option value="" disabled="disabled">Select a year</option>
                          {s.year_list.map(function(year){
                            return <option key={year} value={year}>{year}</option>
                          })}
                        </select>
                      </div>
                      <div className="columns small-12 phone-title">How much can you and your family contribute towards your college education?</div>
                      <div className="columns small-12 medium-8 row_div">
                        <select id="_payment" onChange={this._validate} onFocus={this._validate} value={s.payment||''} className="row-select" 
                          style={s.focused==='_payment' ? (s._paymentValid ? styles.good : styles.bad) : (s._paymentValidated && !s._paymentValid ? styles.bad : styles.good_green)}
                          name="payment">
                          <option value="" disabled="disabled">Select your contribution amount</option>
                          {s.payment_list.map(function(payment){
                            return <option key={payment.value} value={payment.value}>{payment.label}</option>
                          })}
                        </select>
                      </div>
                      <div className="columns small-12 medium-1"></div>
                      <div className="columns small-1 medium-1 row-checkbox">
                          <input type="checkbox" id="_intrest" name="intrest" onChange={this._checkingTheinterest} onFocus={this._validate} value="1" checked={this.state.intrest}
                            style={s.focused==='_intrest' ? (s._intrestValid ? styles.good : styles.bad) : (s._intrestValidated && !s.intrestValid ? styles.bad : styles.good_green)}
                            placeholder=""/>
                      </div>
                      <div className="columns small-11 medium-11">
                          <label htmlFor="_intrest">I am interested in financial aid, grants and scholarships</label>
                      </div>
                      <div className="columns small-12 medium-5 phone-back div-back-step-6">
                          <Link to={s.back_route}>Go Back</Link>
                      </div>
                      <div className="columns small-12 medium-6 medium-text-right">
                          <button className="phone-submit radius button step-6-button" onClick={this._save} disabled={!s.enableConfirmationCode}>Next</button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
              {s.is_sending ? (<Loader/>) : null}
            </span>
          )}
        </div>
      )
    }
}

const mapStateToProps = (state) =>{
  return{
    step: state.steps.step5,
    username: state.steps.username,
  }
}

const mapDispatchToProps = (dispatch) => {
  return {
  }
}

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(Step5));
