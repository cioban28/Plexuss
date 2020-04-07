import React, { Component } from 'react'
import ReactDom from 'react-dom'
import { connect } from 'react-redux'
import '../styles.scss'

class CountryDropDown extends Component {
	render() {
		return (
			<div id="myDropdown" className="dropdown-content" data-reactid=".0.0.2.0.2.1">
				<a href="/college-fairs-events/?country=Austria" data-reactid=".0.0.2.0.2.1.0">
					<img src="/images/flags-mini/Austria.png" data-reactid=".0.0.2.0.2.1.0.0"/>
					<span data-reactid=".0.0.2.0.2.1.0.1">
					</span>
					<span data-reactid=".0.0.2.0.2.1.0.2">
						Austria
					</span>
				</a>
				<a href="/college-fairs-events/?country=Brazil" data-reactid=".0.0.2.0.2.1.1">
					<img src="/images/flags-mini/Brazil.png" data-reactid=".0.0.2.0.2.1.1.0"/>
					<span data-reactid=".0.0.2.0.2.1.1.1">
					</span>
					<span data-reactid=".0.0.2.0.2.1.1.2">
						Brazil
					</span>
				</a>
				<a href="/college-fairs-events/?country=Brazil" data-reactid=".0.0.2.0.2.1.1">
					<img src="/images/flags-mini/Brazil.png" data-reactid=".0.0.2.0.2.1.1.0"/>
					<span data-reactid=".0.0.2.0.2.1.1.1">
					</span>
					<span data-reactid=".0.0.2.0.2.1.1.2">
						India
					</span>
				</a>
			</div>
		);
	}
}

export default CountryDropDown



			