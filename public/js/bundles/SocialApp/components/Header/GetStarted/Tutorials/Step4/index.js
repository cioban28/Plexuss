import React, { Component } from 'react'
import Header from '../Header';
import Footer from '../Footer';
import Card from '../common/card';
class Step4 extends Component{
    render(){
        let { handleRenderComponent } = this.props;
        return(
            <ul className="rightbar-list">
                <Header title={"Connect and Chat"} handleRenderComponent={handleRenderComponent}/>
                <Card href={""} img={'/social/images/Icons/tab-network-sic.svg'} text={'My Network'} imgClass={'find_colleges_img'}/>
                <Card href={""} img={'/social/images/Icons/message-sic.svg'} text={'Messages'} imgClass={'find_colleges_img'}/>
                <Footer/>
            </ul>
        )
    }
}

export default Step4;