import React, { Component } from 'react';
import { connect } from 'react-redux';
import './styles/stats.scss';
import './styles/admissions.scss';
import { getCollegeChat } from '../../../api/search';


class ChatPage extends Component {
  componentDidMount() {
    !this.props.chat & this.props.getCollegeChat(this.props.college.CollegeId);
  }

  render() {
    return (
      <h1>Chat page</h1>
    )
  }
}
