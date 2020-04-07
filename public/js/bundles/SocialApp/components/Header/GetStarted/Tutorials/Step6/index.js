import React, { Component } from 'react'
import Header from './../Header'
import Footer from './../Footer'
class Step6 extends Component{
    render(){
        let { handleRenderComponent } = this.props;
        return(
            <ul className="rightbar-list">
                <Header title={"MyCounselor"} handleRenderComponent={handleRenderComponent}/>
                <Card text={'MyCounselor'}/>
                <Footer/>
            </ul>
        )
    }
}
function Card(props){
    let { number, text } = props;
    let sub = 'Step' + number;
    return(
        <li>
            <div className="need_help_card row">
                <div className="text large-9 medium-9 small-9 columns">
                    {text}
                </div>
            </div>
        </li>
    )
}
export default Step6;