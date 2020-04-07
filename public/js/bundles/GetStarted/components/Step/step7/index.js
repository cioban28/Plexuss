// GetStarted_tcpa/index.js

import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import { connect } from 'react-redux';
import { isEmpty } from 'lodash';

import { getStudentData, updateUserInfo, getTCPAData, saveTCPAData } from './../../../actions/user'
import { SpinningBubbles } from './../../Header/loader/loader'
import './styles.scss';

class GetStartedTCPA extends Component{
    constructor(props){
        super(props);

        this.state = {
			stepView: 'tcpa',
			colleges: {},
			answers: {},
        }

        this._save = this._save.bind(this);
        this._handleCheckbox = this._handleCheckbox.bind(this);
        this._handleInitialAnswers = this._handleInitialAnswers.bind(this);
    }

    componentDidMount() {
		const { dispatch, _user } = this.props;

    	dispatch( getStudentData() );
		dispatch( getTCPAData() );
    }
    componentDidUpdate(prevProps){
		if(this.props._user !== prevProps._user) {
			if (this.props._user.data !== prevProps._user.data) {
				this.setState({colleges: this.props._user.data})
				setTimeout(this._handleInitialAnswers, 100);
			}
		}
    }

    _save() {
		const { dispatch } = this.props;
		let finalAnswers = this.state.answers;
		finalAnswers['step'] = '7';
		// let lead_id = document.getElementById('leadid_token').value;
		// finalAnswers['leadid'] = lead_id;

		dispatch( updateUserInfo(this.state.answers) );
		dispatch( saveTCPAData(finalAnswers) );
    }

    _handleInitialAnswers() {
		Object.values(this.state.colleges).map(colleges => {
			colleges.map(col => {
				let newAnswers = this.state.answers;
				if(col.field_name === null){
					newAnswers["_"+col.college_id+"_"+col.ro_id] = 1;
				}else {
					newAnswers[col.field_name+"_"+col.college_id+"_"+col.ro_id] = 1;
				}
				this.setState({answers: newAnswers})
			})
		})
    }

    _handleCheckbox(event, college) {
		let newAnswers = this.state.answers
		let value = event.target.type === 'checkbox' ? event.target.checked : event.target.value;
		value = value === true ? 1 : 0;
		if(college.field_name === null) {
			newAnswers["_"+college.college_id+"_"+college.ro_id] = value;
		}else {
			newAnswers[college.field_name+"_"+college.college_id+"_"+college.ro_id] = value;
		}

		this.setState({answers: newAnswers});
    }

    render() {
    	const { _user } = this.props;
		return (
			<div>
				{!!_user.init_done === false && <div className="spin-wrap"><SpinningBubbles/></div> }
				{!!_user.init_done === true &&
					<div className="small-12 columns main-container">
						<div className="text-container">Review and Accept each college's <nobr>Terms and Conditions</nobr></div>
						<div className="tcpa-container">
							{this.state.answers !== {} && Object.values(this.state.colleges).map( (college, i) => <TCPACard key={college.college_id +'_'+i} college={college} handleCheckbox={this._handleCheckbox} answers={this.state.answers} /> )}
						</div>
						<div className="next-container" onClick={this._save}>
							<div className={"next-btn"}>{!!_user.save_tcpa_data_pending === true ? <div className="spin-wrap spin-wrap2"><SpinningBubbles/></div> : 'Finish!' }</div>
						</div>
					</div>
				}
			</div>
        );
    }
}

class TCPACard extends Component {
    constructor(props) {
        super(props)
    }

    render() {
        document.body.className = 'step-7'
        const { college, handleCheckbox, answers } = this.props;

        let imgStyle = {
            backgroundImage: 'url('+college[0].college_logo+')',
        }

        return (
            <div className="tcpa-card">
                <div className="row college-row">
                    <div className="small-2 columns">
                        <div className="school-logo" style={imgStyle}/>
                    </div>
                    <div className="small-10 columns college-name">
                        {college[0].school_name ? college[0].school_name : 'N/A'}
                    </div>
                </div>
                {answers !== {} && college.map((question, i) => (
                        <div key={question.college_id + "_" + i } className="row college-row">
                            <div className="small-2 columns">
                            { question.field_name === null ?
                                <input type="checkbox" className="check-box" checked={!!answers["_"+question.college_id+"_"+question.ro_id]} onChange={ (e) => handleCheckbox(e, question) } />
                                :
                                <input type="checkbox" className="check-box" checked={!!answers[question.field_name+"_"+question.college_id+"_"+question.ro_id]} onChange={ (e) => handleCheckbox(e, question) } />
                            }
                            </div>
                            <div className={"small-10 columns college-text"} dangerouslySetInnerHTML={{__html: question.question ? question.question : '' }} />
                        </div>
                    ))
                }
            </div>
        )
    }
}

const mapStateToProps = (state, props) => {
    return {
        _user: state._user,
    }
}

export default connect(mapStateToProps)(GetStartedTCPA);