import React, { Component } from 'react'

import './styles.scss';

class PeopleViewed extends Component{
    render(){

        return(
            <span>
                <div className="peoplpe_viewed_label"> Poeple also viewed</div>
                <ul className="peoplpe_viewed">
                    <ViewedUser name={'Tina Grey'} desc={'College Park High School 17'} connection_number={'2nd'} img={'/images/twitter.png'}/>
                    <ViewedUser name={'Thomas s.'} desc={'University of California Berkeley 10'} connection_number={'1st'} img={'/images/twitter.png'}/>
                    <ViewedUser name={'Sarah Parker'} desc={'College Park High School 17'} connection_number={'3rd'} img={'/images/twitter.png'}/>
                    <ViewedUser name={'Slothy McSlotherson'} desc={'Intern at Codeapalooza'} connection_number={'2nd'} img={'/images/twitter.png'}/>
                </ul>
            </span>
        )
    }
}

class ViewedUser extends Component{

    render(){
        let { name, desc, img, connection_number } = this.props;
        return(
            <li>
                <div className="row viewed_user_card" >
                    <div className="small-2 columns user_img">
                        <img src={img} alt="" />
                    </div>
                    <div className="small-10 columns">
                        <div className="user_name">{name}</div>
                        <div className="connection_number">{connection_number}</div>
                        <div className="description">{desc}</div>
                    </div>
                </div>
            </li>
        )
    }
}
export default PeopleViewed
