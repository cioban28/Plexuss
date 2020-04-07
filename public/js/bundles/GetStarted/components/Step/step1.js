import React, { Component } from 'react';
import { connect } from 'react-redux'
import axios from 'axios';
import Loader from '../Header/loader'
import { getStepStatuses, getStepDatas } from '../../api/step';
import { setUserInfo } from '../../actions/step'
import { SpinningBubbles } from './../Header/loader/loader'
import _ from 'lodash'
import moment from 'moment'
import { withRouter } from 'react-router-dom'

var styles = {
	good: {
		border: '1px solid #24b26b',
	},
	bad: {
		border: '1px solid firebrick',
	}
};

class Step1 extends Component {
    constructor(props) {
      super(props)
      this.state = {
        save_route: '/get_started/save',
        get_route: '/get_started/getDataFor/step',
        step_num: this.props.currentPage,
        is_sending: false,
        back_route: null,
        next_route: null,
        save_btn_classes: 'right btn submit-btn text-center',
        save_has_been_clicked: !1,
      }

      this.updateUser = this.updateUser.bind(this)
      this.save = this.save.bind(this)
      this.formIsValid = this.formIsValid.bind(this)
      this.makeSaveActive = this.makeSaveActive.bind(this)
      this.getUserType = this.getUserType.bind(this)
      this.getGradYr = this.getGradYr.bind(this)
      this.checkForEnterKey = this.checkForEnterKey.bind(this)
      this._setEmail = this._setEmail.bind(this)
    }

    updateUser(e) {
      var val = e.target.value, target= $(e.target),
          user = _.extend({}, this.props.step.step1.user_info), prop = 'is_';
      if(target.attr('name') === 'user_type') {
        prop += val;
        user['is_student'] = 0
        user['is_alumni'] = 0
        user['is_parent'] = 0
        user['is_counselor'] = 0
        user['is_university_rep'] = 0
        user[prop] = 1;
      } else if(target.attr('name') === 'home_schooled') {
        user[target.attr('name')] = target.is(':checked');
      } else if (target.attr('name') === 'edu_level') {
        user.edu_level = val === 'hs' ? 0 : 1;
      } else {
        user.country_id = parseInt(target.val());
      }

      this.props.setUserInfo(user)
    }
    save(e) {
      var formData = new FormData( $('form')[0] ), state = this.state, _this = this;
  
      var user_type = $('select[name=user_type]').val();
  
      if( $(e.target).hasClass('disable') ) e.preventDefault();
      //track if save btn has already been clicked
      if( !state.save_has_been_clicked ) this.setState({save_has_been_clicked : !0})
  
      if( this.formIsValid() ){
        this.setState({is_sending: !0});
  
        $.ajax({
          url: state.save_route,
          type: 'POST',
          data: formData, 
          enctype: 'multipart/form-data',
          contentType: false,
          processData: false,
          headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
          success: function(data) {
            getStepStatuses('')
            .then(() => {
              getStepDatas(_this.state.step_num)
              .then(() => {
                amplitude.getInstance().logEvent('step1_completed', {content: 'Basic Info'} );

                if( JSON.parse(sessionStorage.getItem('college_id')) ){
                  _this.setState({is_sending: !1});//remove loader
                  $(document).trigger('saved');
                } else if (user_type !== 'student') {
                  window.location.href = '/social/edit-profile';
                } else {
                  _this.props.history.push(state.next_route)
                }
              })
            })
          }
        })
      }
    }
    formIsValid(){
      var inputs = $('form .is-input'), valid = !0, val = '', _this = this;
      $.each(inputs, function(){
        val = $(this).val();
        if ($(this).attr('name') === 'school') {
          if ($('#is_home_schooled').is(':checked')) {
              return true;
          } else {
              if (!val) {
                  valid = !1;
                  _this.setState({is_valid: valid}); //set state to false to show error msg
                  return !1;
              }
          }
        } else if( !val ){ //if value is emtpy then make and return false
          valid = !1;
          _this.setState({is_valid: valid}); //set state to false to show error msg
          return !1;
        }
      });
  
      //when valid change state to true to remove error msg
      _this.setState({is_valid: valid});
  
      return valid;
    }

    makeSaveActive(e){
      if( this.formIsValid() ) this.setState({is_valid: !0});
    }
  
    getUserType(){
      var user = this.props.step.step1.user_info,
        type = '', //default to student
        prop;
  
      if( user ){
        for(prop in user ){
          if( user.hasOwnProperty(prop) && prop.indexOf('is') !== -1 ){
            if( parseInt(user[prop]) ) return prop;
          }
        }
      }
  
      return type;
    }

    getGradYr(){
      var user = this.props.step.step1.user_info;
      if( user && parseInt(user.in_college) <= 1 ) return parseInt(user.in_college) ? user.college_grad_year : user.hs_grad_year;
      return '';
    }
  
    checkForEnterKey(){
 
      $('.submit-btn').on('keydown', function(e){
        if( e.which === 13 ) $(this).trigger('click');
      });
    }
  
    _setEmail(_email){
      var user = _.extend({}, this.props.step.step1.user_info, {email: _email});
      this.setState({user_info: user});
    }

    componentWillMount() {
      var prev, next, num;

      // Facebook event tracking
      fbq('track', 'GetStarted_Step1_GeneralInfo_Page');

      //build prev step route
      num = parseInt(this.state.step_num);
      prev = num - 1;
      next = num + 1;
      this.setState({
        back_route: '/get_started/' + prev,
        next_route: '/get_started',
      })

      getStepDatas(this.state.step_num)
      .then(() => {
        this.setState({is_valid: this.props.step.step1.is_valid})
      })
    }
    render() {
      var saveBtnClasses = '', user = this.props.step.step1.user_info,
        intro = user ? ['Tell us a little about yourself, ', <span key={0}>{user.fname}</span>, '...'] : 'Tell us a little about yourself...',
        type = user ? this.getUserType() : '',
        zip = user ? (user.zip || '') : '',
        edu = user && _.isNumber(user.in_college) ? user.in_college : '',
        gender = user && user.gender !== null ? user.gender : '',
        school = user ? (user.school_name || '') : '',
        school_id = user ? (user.current_school_id || '') : '',
        grad = user ? this.getGradYr() : '',
        country = user ? +user.country_id : '',
        hm_school = user && user.home_schooled ? user.home_schooled : !1,
        is_us = !0,
        is_in_college = null;

      if( user && _.isNumber(user.edu_level) ) is_in_college = user.edu_level;
      else if( user && _.isNumber(user.in_college) ) is_in_college = user.in_college;
      else is_in_college = !1;

      if( user && ''+user.country_id !== '1' ) is_us = !1;

      if( !this.state.is_valid ) saveBtnClasses = 'right btn submit-btn text-center disable';
      else saveBtnClasses = 'right btn submit-btn text-center';
      return (
        <div className="step_container">
        { !this.props.step.step1.is_load ? ( <SpinningBubbles/> ) : (
          <div>
          {user && user.email ? (
            <div className="row">
              <div className="column small-12 medium-7">
                <div className="intro">{intro}</div>
                <div><small>There are a few questions we need in order to get you started with Plexuss.</small></div>
                <br />
                <form>
                  <input type="hidden" name="step" value={this.state.step_num} isValid={this.makeSaveActive} />	
                  <SelectInput name="user_type" isValid={this.makeSaveActive} val={type} update={this.updateUser} />
                  <br />
                  <SelectInput name="gender" isValid={this.makeSaveActive} val={gender} update={this.updateUser} />
                  <br />
                  <SelectInput name="edu_level" isValid={this.makeSaveActive} val={edu} usertype={type} update={this.updateUser} />
                  <br />
                  <SelectInput name="grad_yr" isValid={this.makeSaveActive} val={grad} />
                                  { !is_in_college ? <CheckboxInput name="home_schooled" isValid={this.makeSaveActive} val={hm_school} update={this.updateUser} /> : null }
                                  { is_in_college ? <br /> : null }
                                  { !hm_school || is_in_college ? <TextInput name="school" isValid={this.makeSaveActive} val={school} sId={school_id} /> : null }
                                  { !hm_school || is_in_college ? <br /> : null }
                  { !this.state.is_valid && this.state.save_has_been_clicked ? <div className="err"><small>Fields cannot be emtpy.</small></div> : null }

                  <div className="submit-row clearfix">
                    <div tabIndex="0" className={saveBtnClasses} onClick={this.save} onFocus={this.checkForEnterKey}>Next</div>
                  </div>
                </form>
              </div>
              
              <div className="column small-12 medium-5">
                <div className="promo-msg">By completing these steps colleges will be able to recruit you. You will also have the ability to contact colleges directly.</div>
              </div>
            </div>
          ) : (<GetEmail data={this.state} setEmail={this._setEmail} />)}
          </div>
        )}

				{ this.state.is_sending ? <Loader /> : null }
			</div>
      )
    }
}

class GetEmail extends Component {
  constructor(props) {
    super(props)
    this.state = {
      email: '',
      emailValid: false,
      emailValidate: false,
      emailTaken: false,
    }
    this._saveEmail = this._saveEmail.bind(this)
    this._hasEmailNow = this._hasEmailNow.bind(this)
    this._validateEmail = this._validateEmail.bind(this)
  }

  _saveEmail(e) {
    e.preventDefault();

		var formData = new FormData( $('#_no_email_submit')[0] ),
			current_step = this.props.data.step_num, _this=this;

		if( this.state.emailValid ){
			this.setState({is_sending: true});
			$.ajax({
				url: '/get_started/saveEmail',
				type: 'POST',
				data: formData, 
				enctype: 'multipart/form-data',
				contentType: false,
        processData: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			}).done(function(ret){
				// reload this step

				if( ret === 'saved' ){
					_this.setState({
						is_sending: false,
						emailTaken: false,
					});
					//doesn't matter where you're coming from, just go to next possible step
					window.location.href = '/get_started';
				}else if( ret === 'taken' ){
					_this.setState({
						is_sending: false,
						emailTaken: true,
					});
				}else console.log('oops');
			});
		}
  }

  _hasEmailNow() {
    this.props.setEmail(this.state.email)
  }

  _validateEmail(e) {
    this.setState({
      email: e.target.value,
      emailValidate: true,
      emailValid: /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(e.target.value),
    })
  }
  render() {
    var data = this.props.data,
			email = this.state.email,
			eValid = this.state.emailValid,
			eValidated = this.state.emailValidated,
			emailTaken = this.state.emailTaken;

		return (
			<div className="row">
				<div className="column small-12">
					<div className="intro">Please enter your email address</div>
					<br />
					<form id="_no_email_submit" onSubmit={ this._saveEmail }>
						<input
							id="_email_step0"
							className=""
							type="email"
							name="email"
							style={ eValidated ? (eValid ? emailStyles.good : emailStyles.bad) : (emailStyles.good) }
							onChange={ this._validateEmail }
							value={ email || '' }
							placeholder="Email Address" />

						{ emailTaken ? <div style={emailStyles.taken}>This email is already taken. Please choose another email.</div> : null }

						<div className="submit-row clearfix">
							<button
								className={'button radius'} 
								style={emailStyles.btn}
								disabled={ !eValid }
								onClick={ this._saveEmail } >
									Next
							</button>
						</div>
					</form>
				</div>

				{ this.state.is_sending ? <Loader /> : null }
			</div>
		);
  }
}

var emailStyles = {
	good: {
		maxWidth: '300px',
	},
	bad: {
		maxWidth: '300px',
		border: '1px solid firebrick',
	},
	taken:{
		color: 'firebrick',
		fontSize: '14px',
		margin: '0 0 15px',
	},
	btn: {
		background: '#FF5C26',
		padding: '15px 40px',
	}
};

class CheckboxInput extends Component {
  constructor(props) {
    super(props)
    this.state = {
      valu: '',
      options: null,
      checked: !1,
    }
    this.update = this.update.bind(this)
  }

  componentWillMount() {
    if(this.props.val) this.setState({checked: this.props.val})
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.val !== this.props.val) {
      this.setState({checked: nextProps.val})
    }
  }

  update(e) {
    this.props.isValid()
    if(this.props.update) this.props.update(e)
    this.setState({checked: $(e.target).is(':checked')})
  }

  render() {
    var checked = this.state.checked
    return(
      <div className="form-container ckbx">
        <div className="f-input">
          <input id="is_home_schooled" type="checkbox" name={this.props.name} className="is-input"
            onChange={this.update} value="35829" checked={checked || !1}/>
          <label htmlFor="is_home_schooled">Home schooled</label>
        </div>
      </div>
    )
  }
}

class SelectInput extends Component {
  constructor(props) {
    super(props)
    this.state = {
      valu: '',
      options: null,
      isValid: false,
    }
    this.convertVal = this.convertVal.bind(this)
    this.getSelectOptions = this.getSelectOptions.bind(this)
    this.getDataFor = this.getDataFor.bind(this)
    this.buildCountries = this.buildCountries.bind(this)
    this.getLabel = this.getLabel.bind(this)
    this.update = this.update.bind(this)
    this.validate = this.validate.bind(this)
  }

  convertVal(val) {
		if( _.isNumber(val)){
			if( (''+val).length > 1 ) return val;
			else if(this.props.name === 'country') return val;
			else return val ? 'college' : 'hs';
		} else if(_.isString(val) && val.length == 1) {
      return val;
    }else {
      var values = val.split('_')
      values.shift()
      return _.join(values, '_');
    }
	}

	getSelectOptions() {
		var options = [], this_yr = null, end_yr = null, start_yr = null, countries = null, user;

		options.push(<option key={0} value="">{'Select one...'}</option>);
		if( this.props.name === 'user_type' ){
			options.push(<option key={1} value="student">Student</option>);
			options.push(<option key={2} value="alumni">Alumni</option>);
			options.push(<option key={3} value="parent">Parent or Guardian</option>);
			options.push(<option key={4} value="counselor">Counselor or Teacher</option>);
			options.push(<option key={5} value="university_rep">University Rep</option>);
		}else if( this.props.name === 'edu_level' ){
			options.push(<option key={1} value="hs">High School</option>);
      options.push(<option key={2} value="college">College</option>);
		}else if( this.props.name === 'country' ){
    }else if( this.props.name === 'gender' ){
        options.push(<option key={1} value="m">Male</option>);
        options.push(<option key={2} value="f">Female</option>);
		}else{
			this_yr = moment().year();
			start_yr = this_yr - 50;
			end_yr = this_yr + 10;
			for (var i = end_yr; i >= start_yr; i--) {
				options.push(<option key={i} value={i}>{i}</option>);
			}
    }
    this.setState({options: options})

	}

	getDataFor(name){
		$.ajax({
			url: '/get_started/getDataFor/'+name,
			type: 'GET',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		})
		.done(function(data) {
			this.buildCountries(data);
		});
	}

	buildCountries(data){
		var options = [];

		if( data.length > 0 ){
			options.push(<option key={0} value="">{'Select one...'}</option>);
			_.each(data, function(obj){
				options.push(<option key={obj.id} value={obj.id}>{obj.country_name}</option>);
			});
		}

		this.setState({options: options});
	}

	getLabel(){
		var name = this.props.name, type = this.props.usertype;
		if( name === 'user_type' ) return 'I am a(n)...';
		else if( name === 'edu_level' ){
			if( type ){
				if( type.indexOf('student') > -1 ) return 'Current level of education';
				else if( type.indexOf('parent') > -1 ) return 'Your level of education';
				else return 'Highest level of education';
			}
			return 'Current level of education';
		}else if( name === 'country' ){ 
            return 'Your country';
        }else if( name === 'gender' ){
            return 'I am a';
        }else return 'Your year of graduation';
	}

  validate() {
    var valid = false;
    if (this.state.valu !== "") valid = true;
    this.setState({isValid: valid})
    this.props.isValid();
  }

	update(e){
		if( this.props.update ) this.props.update(e);
		this.setState({valu: e.target.value}, ()=>this.validate());
  }

  componentWillReceiveProps(nextProps) {
    if(nextProps.val !== this.props.val) {
      this.setState({valu: this.convertVal(nextProps.val)}, ()=>this.validate())
    }
  }

  componentWillMount() {
    this.getSelectOptions()
    this.setState({
      label: this.getLabel(),
      valu: this.convertVal(this.props.val),
    }, ()=>this.validate())
  }

  render() {
    var Styles = {};

    Styles = !this.state.isValid ? styles.bad : styles.good;
		return (
			<div className="form-container">
				<div className="f-label">
					<label>{this.state.label}</label>
				</div>
				<div className="f-input">
					<select name={this.props.name} className="is-input" style={Styles}
							onChange={this.update} value={this.state.valu} refs={this.props.name}>
						{this.state.options}
					</select>
				</div>
			</div>
		);
	}
}

class TextInput extends Component {
  constructor(props) {
    super(props)
    this.state = {
      valu: '',
      route: '/get_started/searchFor/college',
      list_active: !1,
      schools: [],
      hidden_val: '',
      isValid: false,
      traversing: !1
    }

    this.keypressed = this.keypressed.bind(this)
    this.notTraversing = this.notTraversing.bind(this)
    this.domClick = this.domClick.bind(this)
    this.getPlacholder = this.getPlacholder.bind(this)
    this.getLabel = this.getLabel.bind(this)
    this.update = this.update.bind(this)
    this.buildResults = this.buildResults.bind(this)
    this.addSchool = this.addSchool.bind(this)
  }

  componentWillMount() {
		document.addEventListener('click', this.domClick);
		document.addEventListener('keydown', this.keypressed);
		this.setState({valu: this.props.val, hidden_val: this.props.sId}, ()=>this.validate());
	}

	componentWillReceiveProps(nextProps) {
		//only if TextInput parent passes new val, then setstate with that new value	
		if( nextProps.val !== this.props.val ) this.setState({valu: nextProps.val});
		if( nextProps.sId !== this.props.sId ) this.setState({hidden_val: nextProps.sId});
	}

	keypressed(e) {
		var key = e.which || e.keyCode;

		if( this.state.schools.length > 0 ){
			var container = $('.results-container'), elem = null, results = container.children();
			
			//if elem is valid
			if( key === 40 ){//down
				if( !$('.result:first-child').hasClass('highlighted') && !this.state.traversing ){
					$('.result.highlighted').removeClass('highlighted'); //just in case - clearing all highlighted ones
          $('.result:first-child').addClass('highlighted');
          this.setState({traversing: !0})
				}else $('.result:not(:last-child).highlighted').removeClass('highlighted').next().addClass('highlighted');

				//scroll while traversing
				container.scrollTop( ( $('.result.highlighted').offset().top - container.offset().top ) + container.scrollTop() );
				
			}else if( key === 38 ){ //up key
				$('.result:not(:first-child).highlighted').removeClass('highlighted').prev().addClass('highlighted');
			    container.scrollTop( $('.result.highlighted').offset().top - container.offset().top + container.scrollTop() );
			}else if( key === 13 ) $('.result.highlighted').trigger('click'); //enter key
		}
		
  }
  
  validate() {
    var valid = false;
    if (this.state.valu !== '') valid = true;
    this.setState({isValid: valid})
  }

	notTraversing() {
    this.setState({traversing: !1})
	}

	domClick(e) {
		if( $(e.target).closest('.results-container').length === 0 ){
			this.setState({schools: []});
		}
	}

	getPlacholder() {
		var props = this.props;
		if( props.name === 'zip' ) return 'Zip code';
		else return 'School name';
	}

	getLabel() {
		var name = this.props.name;
		if( name === 'zip' ) return 'Your '+name+' code';
		else return 'Name of your ' + name;
	}

	update(e) {
		var route = null, _this = this
		this.props.isValid();

		if( this.props.name === 'school' ){
      this.notTraversing();
      this.setState({valu: e.target.value}, ()=>this.validate())
			route = this.state.route + '_' + $('select[name="edu_level"]').val();
			$.ajax({
            url: route,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {input: e.target.value},
            type: 'POST'
        }).done(function(data){
				_this.buildResults(data);
			});
		}else{
			this.setState({valu: e.target.value}, ()=>this.validate());
		}
	}

	buildResults(data) {
		var hidden = [], list = [], _this=this

		_.each(data, function(obj, i){
			if( obj.school_name ) 
				list.push(<li className="result" key={obj.id} data-id={obj.id} onClick={_this.addSchool}>{obj.school_name}</li>);
		});

		this.setState({schools: list});
	}

	addSchool(e) {
		var target = $(e.target), val = target.text(),
			id = target.data('id');

		this.setState({
			valu: val,
			hidden_val: id,
			schools: []
		});
	}

	render() {
		var placehldr = this.getPlacholder(), 
			label_name = this.getLabel(),
			classes = this.props.name === 'school' ? 'f-input has-results' : 'f-input', Styles = {};

    Styles = this.state.isValid ? styles.good : styles.bad;
		return (
			<div className="form-container">
				<div className="f-label">
					<label>{label_name}</label>
				</div>
				<div className={classes}>
					{ this.props.name === 'school' ? 
						<input name="school_id" type="hidden" value={this.state.hidden_val} /> : null
					}
					<input name={this.props.name} type="text" placeholder={placehldr} 
							className="is-input" style={Styles} onChange={this.update} value={this.state.valu} onBlur={this.notTraversing} />
						{	this.props.name === 'school' && this.state.schools.length > 0 ?
							<ul className="results-container stylish-scrollbar" onKeyPress={this.keypressed}>
								{ this.state.schools }
							</ul> : null
						}
						
				</div>
			</div>
		);
	}
}

const mapStateToProps = (state) =>{
  return{
    step: state.steps
  }
}

const mapDispatchToProps = (dispatch) => {
  return {
    setUserInfo: (data) => {dispatch(setUserInfo(data))}
  }
}

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(Step1));
