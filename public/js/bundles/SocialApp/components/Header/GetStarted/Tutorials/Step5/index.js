import React, { Component } from 'react'
import Header from '../Header';
import Footer from '../Footer';
import Card from '../common/card';
class Step5 extends Component{
    render(){
        let { handleRenderComponent } = this.props;
        return(
            <ul className="rightbar-list">
                <Header title={"Promote Yourself"} handleRenderComponent={handleRenderComponent}/>
                <Card href={""} img={'/social/images/graduate_male.svg'} text={'Public Profile'} imgClass={'find_colleges_img'}/>
                <Card href={""} img={'/social/images/Icons/write-article.png'} text={'My Articles'} imgClass={'find_colleges_img'}/>
                <Card href={""} img={'/social/images/Icons/tab-home-sic.svg'} text={'Newsfeed'} imgClass={'find_colleges_img'}/>
                <Footer/>
            </ul>
        )
    }
}

export default Step5;