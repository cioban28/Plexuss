import React from "react";
import PropTypes from "prop-types";
import data from "./us-map-dimensions";
import USAState from "./USAState";

class USAMap extends React.Component {

  clickHandler = (stateAbbreviation) => {
    this.props.onClick(stateAbbreviation);
  };

  fillStateColor = (state) => {
    if (this.props.customize && this.props.customize[state] && this.props.customize[state].fill) {
      return this.props.customize[state].fill;
    }

    return this.props.defaultFill;
  };

  stateClickHandler = (state) => {
    if (this.props.customize && this.props.customize[state] && this.props.customize[state].clickHandler) {
      return this.props.customize[state].clickHandler
    }
    return this.clickHandler;
  }

  buildPaths = () => {
    let paths = [];
    for (let stateKey in data) {
      const path = <USAState key={stateKey} onMouseLeave={this.props.onMouseLeave} onMouseEnter={this.props.onMouseEnter} dimensions={data[stateKey]["dimensions"]} state={stateKey} name={data[stateKey]["name"]}  slug={data[stateKey]["slug"]} fill={this.fillStateColor(stateKey)} onClick={this.props.onClick} ISO_A2={data[stateKey]["abbreviation"]} />
      paths.push(path);
    };
    return paths;
  };

  render() {
    return (
      <svg className="us-state-map" xmlns="http://www.w3.org/2000/svg" width={'100%'} height={this.props.height} viewBox="0 0 959 593">
        <title>{this.props.title}</title>
        <g className="outlines">
          {this.buildPaths()}
          <g className="DC state">
            <path className="DC1" fill={this.fillStateColor("DC1")} d="M801.8,253.8 l-1.1-1.6 -1-0.8 1.1-1.6 2.2,1.5z" />
            <circle className="DC2" onMouseEnter={ () => {console.log("hello world")} } onClick={this.props.onClick} data-name={"DC"} fill={this.fillStateColor("DC2")} stroke="#FFFFFF" strokeWidth="1.5" cx="801.3" cy="251.8" r="5" opacity="1" />
          </g>
        </g>
      </svg>
    );
  }
}

USAMap.propTypes = {
  onClick: PropTypes.func.isRequired,
  onMouseEnter: PropTypes.func.isRequired,
  onMouseLeave: PropTypes.func.isRequired,
  width: PropTypes.number,
  height: PropTypes.number,
  title: PropTypes.string,
  defaultFill: PropTypes.string,
  customize: PropTypes.object
};

USAMap.defaultProps = {
  onClick: () => {},
  onMouseEnter: (e) => {console.log("----- mouse enter", e)},
  onMouseLeave: () => { console.log("------mouse leav", e)},
  width: 959,
  height: 593,
  defaultFill: "#D3D3D3",
  title: "Blank US states map",
  customize: {}
};

export default USAMap;