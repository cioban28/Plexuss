import React, { Component } from 'react'
import '../styles.scss'
import axios from 'axios';
import { Link } from 'react-router-dom'
import {Helmet} from 'react-helmet'

class MajorDepartmentsList extends Component {
  is_mount = false;
  constructor(props){
    super(props)
    this.state = {
      departments: [],
      metainfo: {}
    }
  }

  componentDidMount() {
    this.is_mount = true;
    axios.get('/api/college-majors')
      .then(res => {
        const departments = res.data.depts;
        if (this.is_mount)
          this.setState({metainfo: res.data.metainfo, departments: departments})
      }).catch(error => {	
      	console.log("not works");
      });
  }

  componentWillUnmount() {
    this.is_mount = false;
  }

  render() {

		return (
      <div className="row">
        <Helmet>
          <title>{this.state.metainfo.meta_title}</title>
          <meta name='description' content={this.state.metainfo.meta_description} />
        </Helmet>
        <div className="large-12 small-12 medium-12 majors-white-box majors-container-div" style={{float: "right", padding: 0, background: 'none', marginRight: 0}}>
          <div className="dept-container clearfix" style={{marginBottom: "1%"}}>
            {
              !!this.state.departments &&  this.state.departments.length != 0 && this.state.departments.map((department, index) => (
                <div className="dept-box" key={index} style={{width: "16%", marginLeft: 0, marginRight: "0.6%"}}>
                  <Link to={'/college-majors/' + department.slug}>
                    <div className={'dept-img ' + department.slug}></div>
                    <div className="dept-name">{department.name}</div>
                  </Link>
                </div>
              ))
            }
          </div>
        </div>
      </div>
		)
	}
}

export default MajorDepartmentsList