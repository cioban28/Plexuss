import React, { Component } from 'react'
import ReactDom from 'react-dom'
import PlexussCollegeRanking from './PlexussCollegeRanking'
import OthersCollegeRanking from './OthersCollegeRanking'
import axios from 'axios'
import '../styles.scss'

class CollegeRanking extends Component {
	is_mount = false;
	constructor(props) {
		super(props);
		this.state = {
		    plexuss_colleges: [],
		    other_colleges: []
		}
	}
  

  componentDidMount() {
		this.is_mount = true;
    axios.get('/ranking-lists')
      .then(res => {
				if (this.is_mount)
	        this.setState({ plexuss_colleges: res.data.RankingData, other_colleges: res.data.catData });
      }).catch(error => { 
        console.log("not works");
      });
	}

	componentWillUnmount() {
		this.is_mount = false;
	}

	render(){
		return (
	    	<div className="row " data-equalizer>
				<div className = 'margin-from-top-ranking'>
					{ this.state.plexuss_colleges && this.state.plexuss_colleges.length != 0 && <PlexussCollegeRanking plexuss_colleges={this.state.plexuss_colleges} /> }
					{ this.state.other_colleges && this.state.other_colleges.length != 0 && <OthersCollegeRanking other_colleges={this.state.other_colleges} /> }
				</div>
	    	</div>
		)	
	}
}

export default CollegeRanking