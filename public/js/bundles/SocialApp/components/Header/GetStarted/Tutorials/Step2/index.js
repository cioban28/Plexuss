import React, { Component } from 'react'
import Header from './../Header'
import Footer from './../Footer'
import Card from '../common/card'
class Step2 extends Component{
    render(){
        let { handleRenderComponent } = this.props;
        return(
            <ul className="rightbar-list">
                <Header title={"Researh Universites"} handleRenderComponent={handleRenderComponent}/>
                <Card href={""} img={'/social/images/rightBar/Find Colleges@2x.png'} text={'Find Colleges'} imgClass={'find_colleges_img'} />
                <Card href={""} img={'/social/images/rightBar/Majors@2x.png'} text={'Majors'} imgClass={'majors'} />
                <Card href={""} img={'/social/images/rightBar/ranking@2x.png'} text={'Ranking'} imgClass={'ranking'} />
                <Footer/>
            </ul>
        )
    }
}

export default Step2;