import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import { connect } from 'react-redux'
import { getStepStatuses } from '../../api/step';
import './styles.scss'

class Header extends Component{
  constructor(props){
    super(props);

    this.state = {
        num_of_steps: 8,
        max_steps: 5,
        active_step: null,
    }
    this.buildStepCrumbs = this.buildStepCrumbs.bind(this)

  }

  buildStepCrumbs() {
    var stepBreadCrumbs = [], steps = [], obj = null

    var steps_completed = this.props.step.steps_completed;
    var step_num = this.props.step.currentStep !== null ? this.props.step.currentStep : steps_completed.current_step;
    if (step_num > steps_completed.current_step)
        step_num = steps_completed.current_step

    //obj of step props and bool values to show if steps are done or not

    for (var i = 1; i <= this.state.num_of_steps; i++) {
        obj = {
            name: 'Step '+i,
            is_active: parseInt(i) === parseInt(step_num),
            num: parseInt(i),
            currStep: parseInt(step_num),
            done: steps_completed['step_'+i+'_complete'],
            total_num_of_steps: this.state.max_steps
        };

        steps.push(obj);
        if( i <= this.state.max_steps ) stepBreadCrumbs.push(<Step key={i} stepObj={obj} />);
    }
    return stepBreadCrumbs
  }

  componentDidMount() {
    getStepStatuses('')
    // .then(() => {
    //     this.buildStepCrumbs()
    // })
  }
  render() {
      var step_num = this.props.step.currentStep ? this.props.step.currentStep : this.props.step.steps_completed.current_step
      if (!this.props.step.steps_completed.done) {
        if (step_num > this.props.step.steps_completed.current_step)
            step_num = this.props.step.steps_completed.current_step
      } else {
        if(step_num === undefined || step_num === null)
            step_num = 6
      }
      return (
        <span>
            {this.props.step.is_loading ? (null) : (
                <span>
                    <div className="configuring-account-top">
                        <div>Configuring your account, one moment please <span id="configuring-account-count">(3)</span></div>
                        <div className="configuring-account-continue-button">Continue</div>
                    </div>
                    <div className="breadcrumb-header" id="get_started_breadcrumb">
                        <div className="breadcrumb-container text-center clearfix display-flex">
                            <div className="columns logo_parent">
                                <a className="logo" href="/"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/general/plexuss-logo.svg"/></a>
                                <span className="slogan">The Global Student Network</span>
                            </div>
                            {step_num < 6 ? (
                                <div className="large-3 medium-3 columns step-center">
                                { this.props.step.active_step && this.props.step.active_step.num === 8 ?
                                ( <div className="congrats text-left">Congrats you're ready to be recruited!</div> ):
                                ( <div className="breadcrumb-bar clearfix">{this.buildStepCrumbs()}</div> )
                                }
                                </div>
                            ) : (null)}
                        </div>
                    </div>
                </span>
            )}
        </span>
    )
  }
}

class Step extends Component {
    constructor(props) {
        super(props)
        this.ableToRoute = this.ableToRoute.bind(this)
    }

    ableToRoute(e) {
        var this_step = this.props.stepObj
        if (!this_step.done) e.preventDefault();
    }

    render() {
        var step = this.props.stepObj, classes='icon icon-' + step.num, stepClass='step', route = '/get_started/' + step.num;
        if( step.is_active ) stepClass += ' active';
        if( step.done ) stepClass += ' done';
        else route='';
        return (
            <div className={stepClass}>
                <Link to={route} onClick={this.ableToRoute}>
                    <div className={classes}></div>
                    <div className="step-num">Step {step.num} <span className="show-for-small-only"> of {step.total_num_of_steps}</span></div>
                </Link>
            </div>
        )
    }
}

const mapStateToProps = (state) =>{
  return{
      step: state.steps,
  }
}

const mapDispatchToProps = (dispatch) => {
  return {
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(Header);
