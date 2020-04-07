import React , { Component } from 'react'
import GetStarted from './../GetStarted/index'
import RightBar from './../RightBar/index'
import Quad from './../Quad/index'

import './slidingMenu.scss'

class SlidingMenu extends Component{
    constructor(props){
        super(props);
        this.state ={renderComponent: 'get-started'}
        this.handleComponent = this.handleComponent.bind(this);
        this._renderSubComp = this._renderSubComp.bind(this);

    }
    handleComponent(compName){
        this.setState({renderComponent:compName});
    }
    _renderSubComp(){
        let { closeCallback } = this.props;
        switch(this.state.renderComponent){
            case 'get-started' : return <GetStarted closeCallback={closeCallback}/>
            case 'quad' : return <Quad closeCallback={closeCallback} />
            case 'right-bar': return <RightBar closeCallback={closeCallback}/>
        }
    }
    render(){
        return(
            <div className="sliding_menu">
                <div className="upper_bar">
                    <UpperBar imgSrc={'/social/images/Subtraction 1.svg'} activeImgSrc={'/social/images/Icons/sic-getstarted-active.svg'} renderComponent={this.state.renderComponent} compName={'get-started'} handleComponent={this.handleComponent} imgClass={'img'}/>
                    <UpperBar imgSrc={'/social/images/apps-SIC.svg'} activeImgSrc={'/social/images/Icons/sic-quad-active.svg'} renderComponent={this.state.renderComponent} compName={'quad'} handleComponent={this.handleComponent} imgClass={'img'}/>
                    <UpperBar imgSrc={'/social/images/right-nav.png'} activeImgSrc={'/social/images/Icons/sic-more-active.svg'} renderComponent={this.state.renderComponent} compName={'right-bar'} handleComponent={this.handleComponent} imgClass={'right_bar_icon'}/>
                </div>
                {this._renderSubComp()}
            </div>
        )
    }
}

class UpperBar extends Component{
    render(){
        let { imgSrc, activeImgSrc, imgClass, renderComponent, compName, handleComponent } = this.props;
        return(
            <div className={"img_parent " + (renderComponent === compName ? 'active' : '')} onClick={() => handleComponent(compName)}>
                <img src={(renderComponent === compName ? activeImgSrc : imgSrc)} className={imgClass}/>
            </div>
        )
    }
}
export default SlidingMenu