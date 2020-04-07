import React, { Component } from 'react';
import { connect } from 'react-redux'
import Loader from '../Header/loader'
import {withRouter, Link} from 'react-router-dom';
import GPA from './GPA'
import { getStepStatuses, getStepDatas, getCountries } from '../../api/step';
import { SpinningBubbles } from './../Header/loader/loader'
import _ from 'lodash'

class Step2 extends Component {
    constructor(props) {
        super(props)
        this.state = {
          save_route: '/get_started/save',
          get_route: '/get_started/getDataFor/step',
          step_num: this.props.currentPage,
          is_valid: false,
          is_sending: false,
          back_route: null,
          next_route: null,
          save_btn_classes: 'right btn submit-btn text-center',
          save_has_been_clicked: !1,
          unweighted_gpa: '',
          weighted_gpa: '',
          currentView: 'birthday', // birthday || gpa
        }
        this.save = this.save.bind(this)
        this.formIsValid = this.formIsValid.bind(this)
        this.validateForm = this.validateForm.bind(this)
        this.onChangeGPA = this.onChangeGPA.bind(this)
        this.makeSaveActive = this.makeSaveActive.bind(this)
        this.checkForEnterKey = this.checkForEnterKey.bind(this)
    }

    save(e){
      var formData = new FormData( $('form')[0] ), state = this.state, _this = this;

      if( $(e.target).hasClass('disable') ) e.preventDefault();
      //track if save btn has already been clicked
      if( !state.save_has_been_clicked ) state.save_has_been_clicked = !0;

      if( this.validateForm() ){
        this.setState({is_sending: !0});
        $.ajax({
          url: state.save_route,
          type: 'POST',
          data: formData, 
          enctype: 'multipart/form-data',
          contentType: false,
          processData: false,
          headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        }).done(function(data){
          getStepStatuses('')
          .then(()=> {
            getStepDatas(_this.state.step_num)
            .then(() => {
              _this.setState({
                weighted_gpa: _this.props.step.weighted_gpa,
                unweighted_gpa: _this.props.step.unweighted_gpa,
              })
              _this.validateForm();
            })
            amplitude.getInstance().logEvent('step2_completed', {content: 'GPA'} );

            if( JSON.parse(sessionStorage.getItem('college_id')) ){
              _this.setState({is_sending: !1});//remove loader
              $(document).trigger('saved');
            }else _this.props.history.push(state.next_route)
          })
        });
      }
    }

    formIsValid(){

      var inputs = $('form .is-input'), valid = !0, _this = this;

      $.each(inputs, function(){
        if( !$(this).val() ){ //if value is emtpy then make and return false
          valid = !1;
          _this.setState({is_valid: valid}); //set state to false to show error msg
          return !1;
        }
      });

      //when valid change state to true to remove error msg
      _this.setState({is_valid: valid});

      return valid;
    }

    validateForm() {
        var potentialKeys = ['unweighted_gpa', 'weighted_gpa'];
        var that = this;
        var unweighted_gpa_valid = true;
        var weighted_gpa_valid = true;

        _.each(potentialKeys, function(key) {
            var value = parseFloat(that.state[key]);
            switch (key) {
                case 'unweighted_gpa':
                    unweighted_gpa_valid = !!(value && (0.1 <= value && value <= 4.0));
                    break;

                case 'weighted_gpa': 
                    weighted_gpa_valid = !!(!value || (0.1 <= value && value <= 5.0));
                    break;
            }
        });

        this.setState({
            is_valid: weighted_gpa_valid && unweighted_gpa_valid, 
            weighted_gpa_valid: weighted_gpa_valid, 
            unweighted_gpa_valid: unweighted_gpa_valid
        });

        return weighted_gpa_valid && unweighted_gpa_valid;
    }

    onChangeGPA(event) {
        var value = event.target.value;
        var key = event.target.name;
        var inputObject = {};

        inputObject[key] = value;

        this.setState(inputObject, this.validateForm);
    }

    makeSaveActive(e){
      if( this.validateForm() ) this.setState({is_valid: !0});
    }

    checkForEnterKey(){
      var _this = this;

      $('.submit-btn').on('keydown', function(e){
        if( e.which === 13 ) $(this).trigger('click');
      });
    }

    componentWillMount() {
      var classes = this.state.save_btn_classes, prev, next, num, _this = this;

      // Facebook event tracking
          fbq('track', 'GetStarted_Step2_PlannedStart_Page');

      //build prev step route
      num = parseInt(this.state.step_num);

      prev = num - 1;
      next = num + 1;
      this.setState({
        back_route: '/get_started/' + prev,
        next_route: '/get_started/'
      })

      getStepDatas(this.state.step_num)
      .then(() => {
        getCountries()
        .then(() => {
          _this.setState({
            weighted_gpa: this.props.step.weighted_gpa,
            unweighted_gpa: this.props.step.unweighted_gpa,
          })
          _this.validateForm();
        })
      })
    }

    render() {
      var saveBtnClasses = '',
      user = this.props.step.user_info,
      term = user ? (user.planned_start_term || '') : '',
      yr = user ? (user.planned_start_yr || '') : '',
            weighted_border_color = this.state.weighted_gpa_valid ? '#24b26b' : 'firebrick',
            unweighted_border_color = this.state.unweighted_gpa_valid ? '#24b26b' : 'firebrick',
            currentView = this.state.currentView,
            bdayExists = false;



      if(this.props.step.bdayPending == false && this.props.step.day && this.props.step.month && this.props.step.year){
        // this.setState({currentView: 'gpa'});
        currentView = 'gpa';
        bdayExists = true;
      }
  
      if( !this.state.is_valid ) saveBtnClasses = 'right btn submit-btn text-center disable';
      else saveBtnClasses = 'right btn submit-btn text-center';
  
      return (
        <div className="step_container" id="step2">
          <div className="row">
            {this.props.step.bdayPending == true && this.props.step.gpa_load == true &&
              <SpinningBubbles/>}
            { currentView == 'birthday' && this.props.step.bdayPending == false &&
              <BirthdayFields 
                  day={this.props.step.day || ''}
                  month={this.props.step.month || ''}
                  year={this.props.step.year || ''}
                  save_route={this.state.save_route} 
                  go_next_route={() => this.setState({ currentView: 'gpa' })}
                  back_route={this.state.back_route} /> }
            { currentView == 'gpa' && this.props.step.bdayPending == false && this.props.step.gpa_load == false &&
              <div className="column small-12 medium-6 large-7">
                {this.props.step.user_info.country_id === 1 ? (
                  <div>
                    <div className="intro">Enter your (estimated) GPA so far (you can change this later)</div>
                    <br /><br />
                    <form>
                      <input type="hidden" name="step" value={this.state.step_num} />
                        <div>
                          <div style={{display: 'flex', justifyContent: 'space-between', width: '70%'}}>
                            <label for='unweighted_gpa'>
                              Unweighted GPA *
                              <ToolTip>
                                  <div className="tiptitle">Unweighted GPA:</div> 
                                  Measured on a scale of 0 to 4.0 and do not take the difficulty of courses into account. An A in a low-level class and an A received in an AP class both translate into 4.0s.
                              </ToolTip>
                            </label>
                            <input name='unweighted_gpa' value={this.state.unweighted_gpa} onChange={this.onChangeGPA} style={{width: '35%', textAlign: 'center', border: '1px solid ' + unweighted_border_color}} type='number' step='0.01' placeholder='0.10 - 4.00' />
                          </div>

                          <br />

                          <div style={{display: 'flex', justifyContent: 'space-between', width: '70%'}}>
                              <label for='weighted_gpa'>
                                Weighted GPA 
                                <ToolTip>
                                  <div className="tiptitle">Weighted GPA:</div> 
                                  Typically measured on a scale of 0 to 5.0 and takes the difficulty of courses into account. An A in an AP class would be given a 5.0, while an A received in a low-level class would be assigned a 4.0.
                                </ToolTip>
                              </label>
                              <input name='weighted_gpa' value={this.state.weighted_gpa} onChange={this.onChangeGPA} style={{width: '35%', textAlign: 'center', border: '1px solid ' + weighted_border_color}} type='number' step='0.01' placeholder='0.10 - 5.00'/>
                          </div>
                      </div>

                      { !this.state.is_valid && this.state.save_has_been_clicked ? <div className="err"><small>Fields cannot be empty.</small></div> : null }

                      <div className="submit-row clearfix">
                        <Link className="left btn back-btn hide-for-small-only" style={{color: '#9be3ba'}} to={'/get_started/1'}>Go Back</Link>
                        <div tabIndex="0" className={saveBtnClasses} onClick={this.save} onFocus={this.checkForEnterKey}>Next</div>
                        <div className="right text-center btn back-btn show-for-small-only" style={{color: '#9be3ba'}} onClick={() => this.setState({ currentView: 'birthday'})}>Go Back</div>
                      </div>
                    </form>
                  </div>
                ) : (<GPA/> )}
              </div> }
          </div>
          { this.state.is_sending ? <Loader /> : null }
        </div>
      );
    }
}

class BirthdayFields extends React.Component {
  constructor(props) {
      super(props);

      this.state = {
          month: props.month || '',
          day: props.day || '',
          year: props.year || '',
          is_sending: false,
          timeout: null,
      };

      this._onChange = this._onChange.bind(this);
      this._saveBirthday = this._saveBirthday.bind(this);
      this._validateForm = this._validateForm.bind(this);
  }

  _onChange(event) {
      const key = event.target.name.replace('birth_', ''),
          value = event.target.value;
      let next = null;

      clearTimeout(this.state.timeout);

      this.setState({[key]: value});


      if(key == 'month')
        next = this.dInput;
      if(key == 'day')
        next = this.yInput;

      if(next){
        let tm = window.setTimeout(() => {
           //if some amount of time passes -- autofocus next input
           clearTimeout(this.state.timeout);

          next.focus();
        }, 900);

        this.setState({timeout: tm});
    }
  }

  _validateForm() {
      const potentialKeys = ['month', 'day', 'year'],
          { month, day, year } = this.state;

      let age = '',
          dateString = '',
          birthday = null,
          isValid = false,
          regexValid = false;

      if (month === '' || day === '' || year === '') {
          return isValid;
      }

      potentialKeys.forEach((key, index) => {
          dateString += this.state[key];

          if (index !== 2) {
              dateString += '/';
          }
      });

      regexValid = /^(0[1-9]|1[0-2]|[1-9])\/(|[1-9]|0[1-9]|1\d|2\d|3[01])\/(19|20)\d{2}$/.test(dateString);

      if (regexValid) {
          age = moment().diff(dateString, 'years');

          if (age >= 13) {
              isValid = true;
          }

      }

      return isValid;
  }

  _saveBirthday() {
      const { save_route, go_next_route } = this.props,
          { month, day, year } = this.state;

      this.setState({ is_sending: true });
      $.ajax({
          url: save_route,
          type: 'POST',
          data: {step: 'birthday', month, day, year},
          headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
      }).done((response) => {
          this.setState({ is_sending: false });
          go_next_route();
      });
  }

  componentWillReceiveProps(newProps) {
    const { month: newMonth, day: newDay, year: newYear } = newProps,
        { month, day, year } = this.props;

    if (month !== newMonth || day !== newDay || year !== newYear) {
        this.setState({
            day: newDay,
            month: newMonth,
            year: newYear,
        });
    }
  }

  render() {
      const { back_route } = this.props,
          { month, day, year } = this.state,
          isValid = this._validateForm();

      let saveBtnClasses = '';

      if( !isValid ) saveBtnClasses = 'right btn submit-btn text-center disable';
      else saveBtnClasses = 'right btn submit-btn text-center';

      return (
          <div className="column small-12 medium-6 large-7 birthday-step-container">
              <div className="intro" style={{textAlign: 'center'}}>Please enter your birthday</div>

              <div className='birthday-input-container'>
                  <input name='birth_month' value={month} onChange={this._onChange} type='number' placeholder='M' ref={(input) => this.mInput = input} />
                  <span>/</span>
                  <input name='birth_day' value={day} onChange={this._onChange} type='number' placeholder='D' ref={(input) => this.dInput = input}/>
                  <span>/</span>
                  <input name='birth_year' value={year} onChange={this._onChange} type='number' placeholder='Year' ref={(input) => this.yInput = input}/>
              </div>

              { !isValid && <small className='birthday-error-message'>Please insert a valid date (MM/DD/YYYY). Must be 13 years or older.</small> }

              <div className="submit-row clearfix">
                  <div className="left btn back-btn hide-for-small-only"><a href={back_route}>Go Back</a></div>
                  <div tabIndex="0" className={saveBtnClasses} onClick={this._saveBirthday}>Next</div>
                  <div className="right text-center btn back-btn show-for-small-only"><a href={back_route}>Go Back</a></div>
              </div>

              { this.state.is_sending ? <Loader /> : null }
          </div>
      );
  }
}

class ToolTip extends Component {
	constructor(props){
		super(props);

		this.state = {
			hovering: false
		};

  }
  
	componentDidMount(){
		this.tt.addEventListener('mouseenter', () => {
			this.setState({hovering: true});
		})
		this.tt.addEventListener('mouseleave', () => {
			this.setState({hovering: false});	
		})
  }
  
	render(){
		let {hovering} = this.state;

		return (
			<div className="_Tooltip">
				<div className="tooltip" ref={(div) => this.tt=div}>?</div>
				
				{hovering &&
					<div className="tip">
						{this.props.children}
					</div>}
			</div>
		);
	}
}

const mapStateToProps = (state) =>{
  return{
    step: state.steps.step2,
  }
}

const mapDispatchToProps = (dispatch) => {
  return {
  }
}

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(Step2));
