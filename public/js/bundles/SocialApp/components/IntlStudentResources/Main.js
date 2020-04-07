// /Intl_Resources/Main.js

import React, { Component } from 'react'
import { Link } from 'react-router-dom'
import { RESOURCES } from './constants'

class ISR extends Component {
  render(){
    return (
      <section>
        <h5 className="title">International Student Resources</h5>

        <div className="content">
          As an international student studying in the United States, there are many things you will need to know before you make your transition to the US. To aid you in this process, we have compiled a list of information to help you prepare for your move. There are several forms you will need to fill out, interviews to be had, and tests to be taken before you can make your arrangements to attend college in the United States. You will also need to know how to keep your visa from being terminated, as well as the legalities behind working as an international student while in school.
        </div>
        <br />
        <div className="content">Take a look at our International Student Resources Directory to prepare yourself for studying abroad!</div>
        <br />

        <section>
          { RESOURCES.map((r) => <ResourceItem key={r.title} handleSection={this.props.handleSection} item={r} />) }
        </section>

      </section>
    );
  }
}

class ResourceItem extends Component {
  render(){
    let { item, handleSection } = this.props;

    return (
      <div className="rsrc-item">
        <h5 className="title">{ item.title }</h5>
        <div className={"icon "+item.icon} />
        <div className="content">{ item.content }</div>
        <span onClick={() => handleSection(item.step)}><a >Read more </a></span>
      </div>
    );
  }
}

export default ISR;