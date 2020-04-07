// SocialApp/Components/Scholarships/NCSA/info.js

import React, { Component } from 'react';
import axios from 'axios';
import { connect } from 'react-redux';
import { getStudentData } from './../../../../StudentApp/actions/User';
import { NCSA_SPORTS } from './constants';

import './styles.scss';

// Modal for NCSA application
// Modal can(should?) be made into its own file
function Modal(props){
   return (
        <div className={props.className}>
            <div className="modal-overlay-div" onClick={props.onClick} />
            <div className="modal-content-div">
                <div className="modal-dialog-div">
                    {/* Unicode: &#215; or &times; */}
                    <div className="modal-close-btn" onClick={props.onClick}>×</div>
                    {props.children}
                </div>
            </div>
        </div>
    );  
}

// Dropdown for NCSA modal
// Dropdown can(should?) be made into its own file
function Dropdown(props){
    const list = props.list;
    const items = list.map((item) =>
        <li className="dd-list-item" key={item} onClick={props.update}>{item}</li>
    );
    const classes = props.className ?  "dd-wrapper " + props.className : "dd-wrapper";
    return(
        <div className={classes} >
          <div className="dd-header" onClick={props.toggle}>
            <div className="dd-header-title">{props.selected}
                {/* Unicodes: &#x25B2; and &#x25BC; */}
                {props.open ? <span> ▼ </span> : <span> ▲ </span>}
            </div>
          </div>
          {props.open && <ul className="dd-list"> {items} </ul>}
        </div>
    );
}

// Engage for NCSA modal
// Engage can(should?) be made into its own file
function Engage(props){
    return (
        <div className={props.className}>
            <div className="engage-container">
                <div className="engage-text medium-6 columns">Want to apply to this scholarship?</div>
                <div className="engage-btns medium-6 columns">
                    {props.children}
                </div>
            </div>
        </div>
    ); 
}

//Main Component
class NCSA extends Component{
    constructor(props){
        super(props);
        this.state = {
            displayModal: false,
            showForm1: true,
            showForm2: false,
            showForm3: false,
            submittingForm: false,
            formValid: {
                athlete_first_name: 2, 
                athlete_last_name: 2, 
                athlete_email: 2, 
                athlete_phone: 2,
                sport_id: 2,
                graduation_year: 2,
                parent_first_name: 2,
                parent_last_name: 2,
                parent_email: 2, 
                parent_phone: 2, 
                zip: 2,
                athlete_or_parent: 2,
            },
            selectedSport: 'Select a Sport',
            selectedGradYear: 'Select a Year',
            selectedApplicant: 'Athlete or Parent?',
            sportDDIsOpen: false,
            gradYearDDIsOpen: false,
            applicantDDIsOpen: false,
            // Sports as described by NCSA
            // make into constants file
            sports: NCSA_SPORTS,
            gradYears: ['2019','2020','2021','2022'],
            applicant: ['athlete', 'parent'],
            // Recruit details
            recruit: {
                'athlete_first_name': '',
                'athlete_last_name': '',
                'athlete_email': '',
                'athlete_phone': '',
                'graduation_year': '',
                'parent_first_name': '',
                'parent_last_name': '',
                'parent_email': '',
                'parent_phone': '',
                'athlete_or_parent': '',
                'sport_id': '',
                'zip': '',
                'event_id': '20285',
            },
        };

        this.updateGradYearDropdown = this.updateGradYearDropdown.bind(this);
        this.updateSportDropdown = this.updateSportDropdown.bind(this);
        this.updateApplicantDropdown = this.updateApplicantDropdown.bind(this);

        this.handleSubmitForm1 = this.handleSubmitForm1.bind(this);
        this.handleSubmitForm2 = this.handleSubmitForm2.bind(this);
    }

    // Mounting - ajax call for user data
    componentWillMount(){
        let {dispatch, user} = this.props;
        if( !!user && !user.init_done ) dispatch( getStudentData() );
    }

    updateRecruitOnMount(user){
        var tempRecruit = {...this.state.recruit};
        if(!tempRecruit.athlete_first_name && user.fname){
            tempRecruit.athlete_first_name = user.fname;
        }
        if(!tempRecruit.athlete_last_name && user.lname){
            tempRecruit.athlete_last_name = user.lname;
        }
        if(!tempRecruit.athlete_phone && user.phone){
            //enforces phone format (NO non-digits and NO leading 0s)
            let formattedPhone = user.phone.replace(/\D/g,'').replace(/^[0\.]+/, "");
            tempRecruit.athlete_phone = formattedPhone;
        }
        if(!tempRecruit.athlete_email && user.email){
            tempRecruit.athlete_email = user.email;
        }
        if(!tempRecruit.zip && user.zip){
            tempRecruit.zip = user.zip;
        }

        if(this.state.recruit !== tempRecruit){
            this.setState({recruit: tempRecruit});
        }
    }

    // Redirect to sign up
    redirectToSignUp(){
        window.location = window.location='/signup?utm_source=ncsa&utm_content=right_hand_side';
    }


    // FUNCTIONS
    // Modal functions
    showModal(user){
        this.updateRecruitOnMount(user);
        this.setState({displayModal: true}); 
    }

    hideModal(){
        // When closing, set back to first part of form, close all dropdowns, and reset validation messages
        this.setState({displayModal: false, showForm1:true, showForm2:false, showForm3: false, sportDDIsOpen: false, gradYearDDIsOpen: false, applicantDDIsOpen: false,});
        this.resetValidation();
    }

    //Form functions
    prevForm(){
        this.setState({showForm1: true, showForm2: false});
    }

    confirmForm(){
        this.hideModal();
        this.setState({showForm1: true, showForm2: false, showForm3:false});
    }

    resetValidation(){
        let oFormValid = {...this.state.formValid};
        for (var field in oFormValid) {
            if (oFormValid.hasOwnProperty(field)) {
                oFormValid[field] = 2;
            }
        }
        this.setState({formValid: oFormValid});
    }

    validateField(field, value){
        const textFields = ['athlete_first_name', 'athlete_last_name', 'parent_first_name', 'parent_last_name'];
        const emailFields = ['athlete_email', 'parent_email'];
        const phoneFields = ['athlete_phone', 'parent_phone'];
        const dropdownFields = ['graduation_year','sport_id', 'athlete_or_parent'];
        const zipField = 'zip';

        const textRegex = /^[a-zA-Z\-]+$/;
        const emailRegex = /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+$/;
        const phoneRegex = /^[0-9\-]+$/;
        const zipRegex = /^[a-zA-Z0-9\-]+$/;

        let newFormValid = {...this.state.formValid};

        // 0 = empty/missing, 1 = invalid, 2 = valid
        // If value is empty (all text & dropdowns)
        if((value.length == 0) || (dropdownFields.includes(field) && !value)){
            newFormValid[field] = 0;
            this.setState({formValid: newFormValid});
            return false;
        // All text fields with regex
        }else if((textFields.includes(field) && !value.match(textRegex)) ||
                (emailFields.includes(field) && !value.match(emailRegex)) ||
                (phoneFields.includes(field) && !value.match(phoneRegex)) ||
                (field == zipField && !value.match(zipRegex)) ){
            newFormValid[field] = 1;
            this.setState({formValid: newFormValid});
            return false;
        // If input is valid
        }else{
            newFormValid[field] = 2;
            this.setState({formValid: newFormValid});
        }
        return true;
    }

    validateFormPart1(){
        if(!this.validateField('athlete_first_name', this.state.recruit.athlete_first_name) ||
            !this.validateField('athlete_last_name', this.state.recruit.athlete_last_name) || 
            !this.validateField('athlete_email', this.state.recruit.athlete_email) || 
            !this.validateField('athlete_phone', this.state.recruit.athlete_phone) || 
            !this.validateField('graduation_year', this.state.recruit.graduation_year) || 
            !this.validateField('sport_id', this.state.recruit.sport_id))
        {
            return false;
        }
        return true;
    }

    validateFormPart2(){
        if(!this.validateField('parent_first_name', this.state.recruit.parent_first_name) || 
            !this.validateField('parent_last_name', this.state.recruit.parent_last_name) ||
            !this.validateField('parent_email', this.state.recruit.parent_email) ||
            !this.validateField('parent_phone', this.state.recruit.parent_phone) ||
            !this.validateField('zip', this.state.recruit.zip) ||
            !this.validateField('athlete_or_parent', this.state.recruit.athlete_or_parent))
        {
            return false;
        }
        return true;
    }

    handleChange(field, e){
        var tempRecruit = {...this.state.recruit};
        tempRecruit[field] = e.target.value;
        this.validateField(field, e.target.value);
        this.setState({recruit: tempRecruit});
    }

    handleSubmitForm1(e){
        e.preventDefault();
        if(this.validateFormPart1()){
            this.setState({showForm1: false, showForm2: true});
        }
    }

    handleSubmitForm2(e){
        e.preventDefault();
        if(this.validateFormPart2()){
            this.setState({submittingForm: true});
            // this.setState({showForm2: false, showForm3: true});
            // axios to DistributionController@sendNCSAinquiries
            let post = axios({
                method:'post',
                url:'sendNCSAinquiries',
                data: this.state.recruit,
            }).then(function (response){
                // console.log(response);
            }).catch(function (error){
                // console.log(error);
            });

            post.then(() => {
                this.setState({showForm2: false, showForm3: true});
                this.setState({submittingForm: false});
            });

        }
    }

    //Dropdown functions
    toggleList(list){
        // list == gradYearDDIsOpen || sportDDIsOpen || applicantDDIsOpen
        this.setState(prevState => ({
            [list]: !prevState[list],
        }));
    }

    updateGradYearDropdown(e){
        this.setState({ selectedGradYear: e.target.innerHTML,});
        var tempRecruit = {...this.state.recruit};
        tempRecruit.graduation_year = e.target.innerHTML;
        this.setState({recruit: tempRecruit});
        this.validateField('graduation_year', e.target.innerHTML);
        this.toggleList('gradYearDDIsOpen');
    }

    updateSportDropdown(e){
        this.setState({ selectedSport: e.target.innerHTML,});
        // Key == sport_id, value == sport's name
        for(var [key,value] of this.state.sports){
            if(value == e.target.innerHTML){
                var tempRecruit = {...this.state.recruit};
                tempRecruit.sport_id = key;
                this.setState({recruit: tempRecruit});
                break;
            }
        }
        this.validateField('sport_id', e.target.innerHTML);
        this.toggleList('sportDDIsOpen');
    }

    updateApplicantDropdown(e){
        this.setState({ selectedApplicant: e.target.innerHTML,});
        var tempRecruit = {...this.state.recruit};
        tempRecruit.athlete_or_parent = e.target.innerHTML;
        this.setState({recruit: tempRecruit});
        this.validateField('athlete_or_parent', e.target.innerHTML);
        this.toggleList('applicantDDIsOpen');
    }

    // Render Component
    render() {
        let {user} = this.props;
        if(!user){ return null; }

        let modal = this.state.displayModal ? "ncsa-modal" : " ncsa-modal hide";

        let form1 = this.state.showForm1 ? "ncsa-modal-form" : " ncsa-modal-form hide-form";
        let form2 = this.state.showForm2 ? "ncsa-modal-form" : " ncsa-modal-form hide-form";
        let form3 = this.state.showForm3 ? "ncsa-modal-form" : " ncsa-modal-form hide-form";

        return (
            <div id="ncsa">
                <div className="ncsa-main">                    
                    {/* Left Side - title and content */} 
                    <div className="small-12 large-12 column">
                        {/* Mobile Apply */} 
                        <div className="mobile-apply" onClick={() => this.showModal(user)}>
                            <img className="ncsa-apply-img" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social-assets/college/apply.png" />
                            Apply Now!
                        </div>

                        {/* Title & Content */} 
                        <div className="small-12 column ncsa-title">
                            <h1>Next College Student Athlete</h1>
                        </div>

                        <div className="small-12 column ncsa-content">
                            <h6>About</h6>
                            <p>NCSA is the world’s largest and most successful collegiate athletic recruiting network. 
                                NCSA’s +900 teammates leverage exclusive data, proprietary matching algorithms and personal 
                                relationships built over nearly two decades as the industry leader to connect tens of 
                                thousands of college-bound student-athletes to more than 35,000 college coaches nationwide 
                                across 34 sports every year.
                            </p>

                            <h6>Eligibility Requirements</h6>
                            <p>Our commitment is to helping all student-athletes find their best college fit, and 
                                every year we donate our time and services to qualified athletes based on financial need 
                                and to all eligible military veterans. You can learn more about NCSA by applying <span className="here" onClick={() => this.showModal(user)}>here</span>
                            </p>

                            <div className="row">
                                <div className="small-12 text-center column">
                                    <span className="testimonal">“Keep sending prospects our way, you guys are great!”</span><span className="testimonal-name"> – D1 Head Coach</span>
                                </div>
                            </div>
                            

                            <div className="endorsement"><strong>Provided By</strong></div>
                            <img className="ncsa-logo" src="https://www.ncsasports.org/sites/www2.ncsasports.org/themes/sprint/images/logo-ncsa.png" alt="NCSA logo"/>
                            <br/>
                        </div>
                    </div>

                    {/* Right Side - Engage */}
                    <Engage className="ncsa-engage">

                        <div className="ncsa-apply" onClick={() => this.showModal(user)}>
                            <img className="ncsa-apply-img" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social-assets/college/apply.png" />
                            Apply Now!
                        </div>
                    </Engage>
                </div>

                {/* NCSA Application Modal */} 
                <Modal className={modal} onClick={() => this.hideModal()} buttonText="Next" next={() => this.handleSubmit()} >
                    <div className="modal-title"> Join NCSA and Get Recruited! </div>
                        {/* Application Part 1 - Athlete Info */}
                        <form className={form1} onSubmit={this.handleSubmitForm1}>
                            <label className="small-12 large-6 column ncsa-label">Athlete First Name<span className= "required">*</span></label>
                            <div className="small-12 large-6 column ncsa-form-field">
                                <input type="text" value={this.state.recruit.athlete_first_name} onChange={(e) => this.handleChange('athlete_first_name',e)}/>
                                {this.state.formValid.athlete_first_name == 0 && <div className="small-12 column error">Athlete First Name is a required field</div>}
                                {this.state.formValid.athlete_first_name == 1 && <div className="small-12 column error">Not a valid Athlete First Name</div>}
                            </div>

                            <label className="small-12 large-6 column ncsa-label">Athlete Last Name<span className= "required">*</span></label>
                            <div className="small-12 large-6 column ncsa-form-field">
                                <input type="text" value={this.state.recruit.athlete_last_name} onChange={(e) => this.handleChange('athlete_last_name',e)}/>
                                {this.state.formValid.athlete_last_name == 0 && <div className="small-12 column error">Athlete Last Name is a required field</div>}
                                {this.state.formValid.athlete_last_name == 1 && <div className="small-12 column error">Not a valid Athlete Last Name</div>}
                            </div>

                            <label className="small-12 large-6 column ncsa-label">Athlete Email<span className= "required">*</span></label>
                            <div className="small-12 large-6 column ncsa-form-field">
                                <input type="text" value={this.state.recruit.athlete_email} onChange={(e) => this.handleChange('athlete_email',e)}/>
                                {this.state.formValid.athlete_email == 0 && <div className="small-12 column error">Athlete Email is a required field</div>}
                                {this.state.formValid.athlete_email == 1 && <div className="small-12 column error">Not a valid Email</div>}
                            </div>

                            <label className="small-12 large-6 column ncsa-label">Athlete Phone Number<span className= "required">*</span></label>
                            <div className="small-12 large-6 column ncsa-form-field">
                                <input type="text" value={this.state.recruit.athlete_phone} onChange={(e) => this.handleChange('athlete_phone',e)}/>
                                {this.state.formValid.athlete_phone == 0 && <div className="small-12 column error">Athlete Phone Number is a required field</div>}
                                {this.state.formValid.athlete_phone == 1 && <div className="small-12 column error">Not a valid Phone Number</div>}
                            </div>

                            <label className="small-12 large-6 column ncsa-label">High School Graduation Year<span className= "required">*</span></label>
                            <div className="small-12 large-6 column ncsa-form-field">
                                <Dropdown className="gradYear-modal-dropdown" list={this.state.gradYears} open={this.state.gradYearDDIsOpen} selected={this.state.selectedGradYear} toggle={() => this.toggleList('gradYearDDIsOpen')} update={this.updateGradYearDropdown}/>
                                {this.state.formValid.graduation_year == 0 && <div className="small-12 column error">Athlete Graduation Year is a required field</div>}
                            </div>

                            <label className="small-12 large-6 column ncsa-label">Which Sports do you play?<span className= "required">*</span></label>
                            <div className="small-12 large-6 column ncsa-form-field">
                                <Dropdown className="sports-modal-dropdown" list={Array.from(this.state.sports.values())} open={this.state.sportDDIsOpen} selected={this.state.selectedSport} toggle={() => this.toggleList('sportDDIsOpen')} update={this.updateSportDropdown}/>
                                {this.state.formValid.sport_id == 0 && <div className="small-12 column error">Athlete Sport is a required field</div>}
                            </div>

                            <button className="small-12 large-6 column modal-next-btn" type="submit">Next</button>
                        </form>
                        {/* Application Part 2 - Parent Info */}
                        <form className={form2} onSubmit={this.handleSubmitForm2}>
                            <label className="small-12 large-6 column ncsa-label">Parent First Name<span className= "required">*</span></label>
                            <div className="small-12 large-6 column ncsa-form-field">
                                <input type="text" value={this.state.recruit.parent_first_name} onChange={(e) => this.handleChange('parent_first_name',e)}/>
                                {this.state.formValid.parent_first_name == 0 && <div className="small-12 column error">Parent First Name is a required field</div>}
                                {this.state.formValid.parent_first_name == 1 && <div className="small-12 column error">Not a valid Parent First Name</div>}
                            </div>

                            <label className="small-12 large-6 column ncsa-label">Parent Last Name<span className= "required">*</span></label>
                            <div className="small-12 large-6 column ncsa-form-field">
                                <input type="text" value={this.state.recruit.parent_last_name} onChange={(e) => this.handleChange('parent_last_name',e)}/>
                                {this.state.formValid.parent_last_name == 0 && <div className="small-12 column error">Parent Last Name is a required field</div>}
                                {this.state.formValid.parent_last_name == 1 && <div className="small-12 column error">Not a valid Parent Last Name</div>}
                            </div>

                            <label className="small-12 large-6 column ncsa-label">Parent Email<span className= "required">*</span></label>
                            <div className="small-12 large-6 column ncsa-form-field">
                                <input type="text" value={this.state.recruit.parent_email} onChange={(e) => this.handleChange('parent_email',e)}/>
                                {this.state.formValid.parent_email == 0 && <div className="small-12 column error">Parent Email is a required field</div>}
                                {this.state.formValid.parent_email == 1 && <div className="small-12 column error">Not a valid email</div>}
                            </div>

                            <label className="small-12 large-6 column ncsa-label">Parent Phone Number<span className= "required">*</span></label>
                            <div className="small-12 large-6 column ncsa-form-field">
                                <input type="text" value={this.state.recruit.parent_phone} onChange={(e) => this.handleChange('parent_phone',e)}/>
                                {this.state.formValid.parent_phone == 0 && <div className="small-12 column error">Parent Phone Number is a required field</div>}
                                {this.state.formValid.parent_phone == 1 && <div className="small-12 column error">Not a valid phone number</div>}
                            </div>

                            <label className="small-12 large-6 column ncsa-label">Zip/Postal Code<span className= "required">*</span></label>
                            <div className="small-12 large-6 column ncsa-form-field">
                                <input type="text" value={this.state.recruit.zip} onChange={(e) => this.handleChange('zip',e)}/>
                                {this.state.formValid.zip == 0 && <div className="small-12 column error">Zip/Postal Code is a required field</div>}
                                {this.state.formValid.zip == 1 && <div className="small-12 column error">Not a valid Zip/Postal Code</div>}
                            </div>

                            <label className="small-12 large-6 column ncsa-label">Are you the Athlete or Parent?<span className= "required">*</span></label>
                            <div className="small-12 large-6 column ncsa-form-field">
                                <Dropdown className="applicant-modal-dropdown" list={this.state.applicant} open={this.state.applicantDDIsOpen} selected={this.state.selectedApplicant} toggle={() => this.toggleList('applicantDDIsOpen')} update={this.updateApplicantDropdown}/>
                                {this.state.formValid.athlete_or_parent == 0 && <div className="small-12 column error">Athlete or Parent is a required field</div>}
                            </div>

                            <div className="small-12 large-6 column modal-prev-btn" onClick={() => this.prevForm()}>Back</div>
                            <button className="small-12 large-6 column modal-finish-btn" type="submit" disabled={this.state.submittingForm} >Finish</button>
                        </form>
                    {/* Application Part 3 - Confirmation */}
                    <div className={form3}>
                        <div className="small-12 column"> Thank you! Your information has been submitted to NCSA and their network of coaches. 
                            You will receive an email from them shortly
                        </div>
                        <div className="small-12 column modal-ok-btn" onClick={() => this.confirmForm()}>OK</div>
                    </div>
                </Modal>
            </div>
        );
    }
}

const mapStateToProps = (state, props) => {
    return {
       user: state.user.data
    }
}


export default connect(mapStateToProps)(NCSA);
