// /Intl_Resources/ListGenerator.js

import React from 'react'

class ListGenerator extends React.Component{
	
	render(){
		let { chklist, openUpgrade, upgradeOrVisit } = this.props;

		return (
			<div className="content">
				<h6 className="section-head">{ chklist.title }</h6>
				<ul>{ chklist.list && chklist.list.map((b) => <BulletPoint key={b.title} bullet={b} openUpgrade={openUpgrade} upgradeOrVisit={upgradeOrVisit} />) }</ul>
			</div>
		);
	}
}

class BulletPoint extends React.Component {
	render(){
		let { bullet, openUpgrade, upgradeOrVisit } = this.props;

		return (
			<li className="content">
				{ bullet.title }
				<ul>{ bullet.list && bullet.list.map((n) => <NestedPoint key={n.title || n.content} nested={n} openUpgrade={openUpgrade} upgradeOrVisit={upgradeOrVisit} />) }</ul>
			</li>
		);
	}
}

class NestedPoint extends React.Component {
	_execute(fn){
		let {openUpgrade, upgradeOrVisit} = this.props;
		switch(fn){
			case 'upgradeOrVisit':
				upgradeOrVisit();
		}
	}
	render(){
		let { nested, openUpgrade } = this.props;
		return (
			<li className="content">
				{nested.title || 
					( nested.link ? 
						<a className="premium" href={nested.link}>{nested.content}</a> 
						: nested.button ?
							<div className="list-custom-button" onClick={()=> this._execute(nested.button.func) }>{nested.button.text}</div>
						    : nested.content )}
				<ul>{ nested.list && nested.list.map((l) => <li key={l.content} className="content">{l.content} {l.add_or && <b>OR</b> }</li>) }</ul>
			</li>
		);
	}
}

export default ListGenerator;