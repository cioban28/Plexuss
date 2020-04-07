import React, { Component } from 'react';
import './styles.scss'
import { Link } from 'react-router-dom'

class College extends Component {
	constructor(props) {
		super(props)
		this.state={
			showLinks: false
		}
	}

	handleQuickLinksClick = () => {
    this.setState((prevState) =>({
      showLinks: !prevState.showLinks
    }))   
  }
	render() {
		let college=this.props.college,
			index = this.props.identity
		return(
			<div className='row pt20' key={index}>
				<div className="large-2 small-3 column text-center">
					<img src={'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/' + college.logo_url } className="college_logo" alt="" />
				</div>
        <div className="large-10 small-9 column pr10">
          <span className="c-blue fs18 f-bold">
          	<Link to={'/college/' + college.slug} className="c-blue">{college.school_name}</Link>
       		</span>&nbsp;<span className="flag flag-us"> </span>
          <span className="c79 fs12 d-block mt10 l-hght18">{`
        		Acceptance rate: ${college.percent_admitted}%  |  In-state Tuition: $ ${college.tuition_avg_in_state_ftug}
          	|  Total Enrolled Students: ${college.undergrad_total}  |   Plexuss Rank #3`} <br/> {college.city + ','}
          	<span className="f-bold">{college.state}</span>  |  <span className="c-blue fs12 quick-linker" style={{cursor:'pointer'}} onClick={() => this.handleQuickLinksClick()}>open quick links  <span className="expand-toggle-span run" id="quick-link-div-4480">&nbsp;</span> 
            </span>
        	</span>
      		<div className="row d-none" id="quick-link-4480">
      		{
      			this.state.showLinks && 
            <ul className="quick-link-ul ">
							<li className="large-4 small-12 medium-4" style={{float: 'left'}} ><Link to={`/college/${college.slug}/admissions`} className="c-blue">Admissions</Link></li>
							<li className="large-4 small-12 medium-4" style={{float: 'left'}} ><Link to={`/college/${college.slug}/ranking`} className="c-blue">Ranking</Link></li>
							<li className="large-4 small-12 medium-4" style={{float: 'left'}} ><Link to={`/college/${college.slug}/financial-aid`} className="c-blue">Financial Aid</Link></li>
            </ul>
          }
      		</div>
        </div>
			</div>






			)
	}

} 

export default College
