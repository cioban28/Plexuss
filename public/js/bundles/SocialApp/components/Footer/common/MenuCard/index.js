import React, { Component } from 'react'
import { Link } from 'react-router-dom'
import './styles.scss'
class MenuCard extends Component{
    render(){
        let { message, img, redirect } = this.props;
        return(
            <Link to={redirect}>
                <li>
                  <div className="row menu_card" href="">
                    <div className="small-2 columns">
                        <img src={img} alt="" className="menu_icon"/>
                    </div>
                    <div className="small-9 columns">
                        <div className="message">{message}</div>
                    </div>
                    <div className="small-1 columns">
                        <div> <img src="/images/mobile_menu_arrow.png" className="arrow_img" alt=""/> </div>
                    </div>
                  </div>
                </li>
            </Link>
        )
    }
}
export default MenuCard
