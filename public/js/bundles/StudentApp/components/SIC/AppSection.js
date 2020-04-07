import React, { Component } from 'react';
import { Link } from 'react-router';

export default class AppSection extends Component {
    constructor(props) {
        super(props);
    }

    _buildRoute = (route, index) => {
        const { type, _profile } = this.props;

        switch (type) {
            case 'complete':
                return (
                    <li key={index} className="s-item">
                        <Link key={index} to={ route.path + window.location.search } className={"route "+route.id} activeClassName="active-tab">
                            <span>&#10003;</span> { route.name }
                        </Link>
                    </li>
                );

            case 'optional':
                if (_profile[route.id + '_form_done']) {
                    return (
                        <li key={index} className="s-item">
                            <Link key={index} to={ route.path + window.location.search } className={"route "+route.id} activeClassName="active-tab">
                                <span>&#10003;</span> { route.name }
                            </Link>
                        </li>
                    );
                } else {
                    return (
                        <li key={index} className="s-item">
                            <span key={index} className={route.id}>{route.name}</span>
                        </li>
                    );
                }

            default: // Used for incomplete case for now
                return (
                    <li key={index} className="s-item">
                        <span key={index} className={route.id}>{route.name}</span>
                    </li>
                );
        }

    }

    render() {
        const { type, routes, open } = this.props;

        return ( open ) && 
            <ul className={"sections-list "+(type || '')}>
                { routes && routes.map(this._buildRoute) }
            </ul>
    }
}
