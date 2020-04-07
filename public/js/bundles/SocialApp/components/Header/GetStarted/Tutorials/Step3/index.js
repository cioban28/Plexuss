import React, { Component } from 'react'
import Header from '../Header';
import Footer from '../Footer';
import Card from '../common/card';
class Step3 extends Component{
    render(){
        let { handleRenderComponent } = this.props;
        return(
            <ul className="rightbar-list">
                <Header title={"Select Your Colleges"} handleRenderComponent={handleRenderComponent}/>
                <Card href={""} img={'/social/images/rightBar/noun_college_129978_000000@2x.png'} text={'My Colleges'} imgClass={'find_colleges_img'}/>
                <Footer/>
            </ul>
        )
    }
}

export default Step3;