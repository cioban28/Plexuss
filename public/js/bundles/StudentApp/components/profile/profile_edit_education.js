import React, {Component} from 'react';
import { connect } from 'react-redux';
import { isEmpty, findIndex } from 'lodash';
import Edit_section from './profile_edit_section';
import ClickOutside from './../../../utilities/clickOutside';
import Profile_edit_privacy_settings from './profile_edit_privacy_settings';
import EditEducation from './EditEducation';

export default class Profile_edit_education extends Component{
	constructor(props){
		super(props);

		let {education} = this.props._profile;

		this.state = {
			origEducation: [...education],
			education: [],

			submittable: true,
			newEduId: -1,
		}

		this._convertEdus = this._convertEdus.bind(this);

		this._addEdu = this._addEdu.bind(this);
		this._removeEdu = this._removeEdu.bind(this);
		this._updateEdu = this._updateEdu.bind(this);

		this._valid = this._valid.bind(this);
		this._save = this._save.bind(this);
		this._cancel = this._cancel.bind(this);
	}
	componentDidMount(){
		let newArray = this._convertEdus(this.props._profile.education);
		this.setState({education: newArray});
	}
	componentDidUpdate(prevProps, prevState){
		if(this.state.education !== prevState.education){
			this._valid();
		}
	}
	_convertEdus(array){
		let newArray = array.map(edu => {
			let obj = {
				id: edu.id,
				edu_level: edu.edu_level,
				school_name: !!edu.edu_level ? edu.college && edu.college.school_name : edu.highschool && edu.highschool.school_name ,
				school_id: !!edu.edu_level ? edu.college && edu.college.id : edu.highschool && edu.highschool.id,
				grad_year: edu.grad_year,
				degree_type: edu.degree_type,
				majors: [...edu.majors],
				is_valid: true,
			}
			return obj;
		})
		return newArray;
	}

	_addEdu(){
		let newEdu = {
			id: this.state.newEduId,
			edu_level: -1,
			school_id: null,
			school_name: '',
			degree_type: 0,
			grad_year: '',
			majors: [],
			is_valid: false,
		}
		let newArray = [...this.state.education, newEdu];
		this.setState({education: newArray, newEduId: this.state.newEduId-1});
	}
	_removeEdu(index){
		let newArray = [...this.state.education];
		newArray = newArray.filter(edu => edu.id !== index);
		this.setState({education: newArray});
	}
	_updateEdu(edu, index){
		let newArray = [...this.state.education];
		let tempIndex = newArray.findIndex(edu => edu.id === index)
		newArray[tempIndex] = edu;
		this.setState({education: newArray});
	}

	_valid(){
		let { education } = this.state;
		if(education.length === 0) {
			if(this.state.submittable === false) this.setState({submittable: true});
			return;
		}
		else if(education.some(edu => edu.is_valid === false)) {
			if(this.state.submittable === true) this.setState({submittable: false});
			return;
		}
		if(this.state.submittable === false) this.setState({submittable: true});
	}

	_save(callback){
		let {save}  = this.props;
		let data = {
			data: [...this.state.education],
		};

		save(data, callback);
	}

	_cancel(){
		let { _profile } = this.props;
		if(this.state.origEducation !== _profile.education){ this.setState({education: this._convertEdus([..._profile.education]) }); }
	}

	render(){
		let {education, submittable} = this.state;
		let {_profile, findSchools, searchForMajors} = this.props;

		return(
			<Edit_section autoOpenEdit={this.props.autoOpenEdit} editable={true} saveHandler={this._save} onCancelEditing={this._cancel} section="education" submittable={submittable}>

			{/* Preview Section */}
				<div>
					<div className="green-title">Education
						<Profile_edit_privacy_settings section="education" initial={!!_profile.public_profile_settings ? !!_profile.public_profile_settings.education ? _profile.public_profile_settings.education : null : null}/>
					</div>
					<div className="mt30"></div>
					{_profile.education.map((edu, i) => 
							<EducationPreview key={i} education={edu}/>
						)
					}
				</div>

			{/* Editing Section */}
				<div>
					<div className="green-title">Edit Education</div>
					<div className="mt30"></div>

					{education.length > 0 && education.map( (edu, index) => (
						<div key={edu.id}>
							<div className="remove-edu-school"><span onClick={() => this._removeEdu(edu.id)}>Remove</span></div>
							<EditEducation index={edu.id} education={edu} _profile={_profile} findSchools={findSchools} searchForMajors={searchForMajors} updateEdu={this._updateEdu}/>
						</div>
					))}

					<div className="add-edu-school"><span onClick={()=> this._addEdu()}>Add School</span></div>
				</div>

            </Edit_section>
        );
    }
}

function EducationPreview(props) {
	let { education } = props;
	return (
		<div className="row edu-row">
			<div className="small-6 medium-3 columns edu-col">
				<div className="edu-category">{ education.edu_level === 0 ? 'High School Name' : education.edu_level === 1 ? 'College Name' : '' }</div>
				<div className="edu-value">{ education.edu_level === 0 ? !!education.highschool && education.highschool.school_name : education.edu_level === 1 ? !!education.college && education.college.school_name  : 'N/A' }</div>
			</div>
			<div className="small-6 medium-3 columns edu-col">
				<div className="edu-category">Graduation Year</div>
				<div className="edu-value">{ !!education.grad_year && education.grad_year }</div>
			</div>
			{education.edu_level === 1 &&
				<span>
					<div className="small-6 medium-3 columns edu-col">
						<div className="edu-category">Field of Study</div>
						<div className="edu-value">{ !!education.majors && education.majors.map(major => <div>{major.major_name}</div>) }</div>
					</div>
					<div className="small-6 medium-3 columns edu-col">
						<div className="edu-category">Degree</div>
						{(() => {
							switch(education.degree_type){
								case 1: return <div className="edu-value">Certificate</div>;
								case 2: return <div className="edu-value">Associate's</div>;
								case 3: return <div className="edu-value">Bachelor's</div>;
								case 4: return <div className="edu-value">Master's</div>;
								case 5: return <div className="edu-value">Doctorate</div>;
								case 6: return <div className="edu-value">Undecided</div>;
								default: return <div className="edu-value"></div>;
							}
						})()}
					</div>
				</span>
			}
		</div>
	)
}

// export default connect(mapStateToProps)(Profile_edit_education);