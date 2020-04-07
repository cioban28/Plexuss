// manage_portals.js

import React from 'react';
import { Link } from 'react-router';
import createReactClass from 'create-react-class'

export default createReactClass({
  render() {
    return (
      <div>
        <div onClick={this.props.increment}>Manage portals</div>
        <ul>
          <li><Link to="/admin/profile" activeClassName="active">Profile {this.props.num}</Link></li>
        </ul>
      </div>
    );
  }
});
