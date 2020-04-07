import React, { Component } from 'react';
import { connect } from 'react-redux'
import { setCurrentStep } from '../../actions/step'
import Step1 from './step1'
import Step2 from './step2'
import Step3 from './step3'
import Step4 from './step4'
import Step5 from './step5'
import Step6 from './step6'
import GetStartedTCPA from './step7/index'
import { SpinningBubbles } from './../Header/loader/loader'

class Step extends Component {
    constructor(props) {
        super(props)
        this.state = {
            currentPage: this.props.match.params.step ? this.props.match.params.step : null,
        }
        this.subRender = this.subRender.bind(this)
    }

    componentDidMount() {
        if (this.state.currentPage === null || this.state.currentPage === undefined) {
            this.setState({currentPage: this.props.step.steps_completed.current_step})
        }
        var currentPage = this.state.currentPage
        if(currentPage === undefined) currentPage = this.props.step.steps_completed.current_step
        if (!this.props.step.steps_completed.done) {
            if (currentPage > this.props.step.steps_completed.current_step)
                currentPage = this.props.step.steps_completed.current_step
        } else {
            if (currentPage === null || currentPage === undefined)
                currentPage = 6
        }
        this.props.setCurrentStep(currentPage)
    }

    componentDidUpdate(prevProps) {
        if (prevProps.location !== this.props.location) {
            this.setState({currentPage: this.props.match.params.step ? this.props.match.params.step : this.props.step.steps_completed.current_step})
            this.props.setCurrentStep(this.props.match.params.step ? this.props.match.params.step : this.props.step.steps_completed.current_step)
        }
    }
    subRender(currentPage) {
        var page = '' + currentPage
        switch(page) {
            case "1": return <Step1 currentPage={page}/>
            case "2": return <Step2 currentPage={page}/>
            case "3": return <Step3 currentPage={page}/>
            case "4": return <Step4 currentPage={page}/>
            case "5": return <Step5 currentPage={page}/>
            case "6": return <Step6 currentPage={page}/>
            case "7": return <GetStartedTCPA currentPage={page}/>
        }
    }

    render() {
        var currentPage = this.state.currentPage
        if(currentPage === undefined || currentPage === null) currentPage = this.props.step.steps_completed.current_step
        if (!this.props.step.steps_completed.done) {
            if (currentPage > this.props.step.steps_completed.current_step)
                currentPage = this.props.step.steps_completed.current_step
        } else {
            if(currentPage === undefined || currentPage === null)
                currentPage = 6
        }
        return (
            <div>
                {this.props.step.is_loading ? (<SpinningBubbles/>) : (<span>{this.subRender(currentPage)}</span>)}
            </div>
        )
    }
}

const mapStateToProps = (state) =>{
  return{
      step: state.steps
  }
}

const mapDispatchToProps = (dispatch) => {
  return {
      setCurrentStep: (step)=>{dispatch(setCurrentStep(step))}
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(Step);
