import React, { Component } from 'react'
import './styles.scss'
import MajorsListItem from './majorsLikstItem'
import { Link } from 'react-router-dom'

class MajorsList extends Component {
  state = {
   showMajorslist: false,
   hover: false
  }

  handleMouseEnterLeave = () => {
    this.setState((prevState) => ({
			showMajorslist: false
		}))
  }

  hoverOn = (i) => {
    this.setState({ hover: true });
  }
  hoverOff = (i) => {
    this.setState({ hover: false });
  }

	render(){
    let {department, selected, selected_major, allDepartmentsWithMajors, identity} = this.props 
    return (
        <div onMouseEnter={() => this.hoverOn(identity)} onMouseLeave={() => this.hoverOff(identity)}>

              <div ><Link className={(selected == department.url_slug || this.state.hover==true) ? 'active' : ''} to={'/college-majors/' + department.url_slug} >{department.name}</Link></div>

              {
                <ul  className="ul-hover-styling ui-menu ui-widget ui-widget-content ui-front" role="menu" aria-expanded="true">
                    <MajorsListItem showMajorslist={this.state.hover} current_department={department.url_slug} selected={selected} selected_major={selected_major} majors={ allDepartmentsWithMajors[department.name].majors}  key={identity} />
                </ul>
              }


        </div>
    )
  }
}

export default MajorsList