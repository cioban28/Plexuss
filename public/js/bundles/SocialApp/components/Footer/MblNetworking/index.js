import React, { Component } from 'react'
import './styles.scss'
import MenuHeader from './../common/MenuHeader/index'
import MenuCard from './../common/MenuCard/index'
class Networking extends Component {
    render(){
        return(
            <div>
                <div className="mbl_banner">
                    <MenuHeader title={'NETWORKING'}/>
                    <ul className="mbl_list">
                        <MenuCard img={'/social/images/rightBar/Find Colleges.png'} redirect={'/social/networking/connection'} message={'274 Connections'}/>
                        <MenuCard img={'/social/images/rightBar/Find Colleges.png'} redirect={'/social/networking/importContacts'} message={'Import Contacts'}/>
                        <MenuCard img={'/social/images/rightBar/Find Colleges.png'} redirect={'/social/networking/suggestion'} message={'Suggestions'}/>
                        <MenuCard img={'/social/images/rightBar/Find Colleges.png'} redirect={'/social/networking/requests'} message={'Request'}/>
                    </ul>
                </div>
            </div>
        )
    }
}

export default Networking
