import React, { Component } from 'react'
import { Link } from 'react-router-dom'
import './styles.scss'

class MajorsListItem extends Component {
  state = {
   showMajorslist: false,
   hov: true
  }

  hoverOn = () => {
    this.setState({ hov: true });
  }
  hoverOff = () => {
    this.setState({ hov: false });

  }

	render(){
    let {majors, showMajorslist, current_department, selected, selected_major} = this.props 
    return (
       !!showMajorslist ?
       <div> 
         <li onMouseEnter={() => this.hoverOn(0)} onMouseLeave={() => this.hoverOff(0)} key={this.props.identity} className="ui-menu-item" key={0} id="ui-id-5" tabIndex="-1" role="menuitem">
              <div>
                <Link className={(!selected_major && current_department == selected || this.state.hover==true) ? 'active' : ''} to={`/college-majors/${current_department}`}>Overview</Link>
              </div>
            </li>
        {majors.map( (major, index) => 
            <li onMouseEnter={() => this.hoverOn(index+1)} onMouseLeave={() => this.hoverOff(index+1)} key={this.props.identity + 1} className="ui-menu-item" key={index+1} id="ui-id-5" tabIndex="-1" role="menuitem">
              <div>
                <Link className={(selected_major == major.slug || this.state.hover==true) ? 'active' : ''} to={`/college-majors/${major.mdd_slug}/${major.slug}`}>{major.name}</Link>
              </div>
            </li>
        )}
        </div>
        :
        null
    )
  }
}

export default MajorsListItem