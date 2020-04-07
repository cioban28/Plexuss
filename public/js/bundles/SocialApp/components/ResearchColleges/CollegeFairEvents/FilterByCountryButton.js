import React, { Component } from 'react'
import ReactDom from 'react-dom'
import { connect } from 'react-redux'
import '../styles.scss'

class FilterByCountryButton extends Component {
	render() {
		return (
				<button className="dropbtn dropcountry" data-reactid=".0.0.2.0.2.0">
					<span className="fill-country" data-reactid=".0.0.2.0.2.0.0">
						Filter by country
					</span>
					<span className="navigation-arrow-down11" data-reactid=".0.0.2.0.2.0.1">
					</span>
				</button>
			);
	}
}

export default FilterByCountryButton



			