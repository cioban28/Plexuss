import React, { Component } from 'react'
import './styles.scss'
class MenuHeader extends Component{
    render(){
        let { title } = this.props
        return(
            <div className="menu_header">{title}</div>
        )
    }
}
export default MenuHeader