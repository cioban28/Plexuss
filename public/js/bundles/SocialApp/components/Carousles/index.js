import React, { Component } from 'react';
import { connect } from 'react-redux'
import Near from './Near'
import Ranking from './Ranking'
import Virtual from './Virtual'
import News from './News'
import Feature from './Feature'

class Carousles extends Component{
  constructor(props){
    super(props);

    this.state = {
    }
    this.buildCarousles = this.buildCarousles.bind(this)
  }

  buildCarousles() {
      switch(this.props.tag) {
          case "near":
              return <Near />
          case "ranking":
              return <Ranking />
          case "virtual":
              return <Virtual />
          case "news":
              return <News />
          case "feature":
              return <Feature />
      }
  }

  componentDidMount(){
  }

  componentWillMount() {
  }

  render() {
    return (
        <span>
        {this.buildCarousles()}
        </span>
    );
  }
}

const mapStateToProps = (state) =>{
  return{
      carousles: state.carousles,
  }
}

const mapDispatchToProps = (dispatch) => {
  return {
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(Carousles);
