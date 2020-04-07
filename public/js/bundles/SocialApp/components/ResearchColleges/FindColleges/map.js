import React, { Component } from 'react'
import tooltip from "wsdm-tooltip"
import { connect } from 'react-redux';
import { withRouter } from 'react-router-dom'
import {
  ComposableMap,
  ZoomableGroup,
  Geographies,
  Geography,
} from "react-simple-maps"
import USAMap from "./USAMap/index";
import {states} from './stateNames'
import Dropdown from 'react-dropdown'
import './react-dropdown.scss'
import { getAllCountries } from '../../../api/findColleges'


class WorldMap extends Component{
  constructor(props){
    super(props)
    this.state={
      showUSAMap: false,
      isMobile: false,
      isUSSelected: false,
      selectedCountry: '',
      selectedState: '',
    }

    this.handleCountrySelection = this.handleCountrySelection.bind(this);
    this.handleStateSelection = this.handleStateSelection.bind(this);
    this.handleMapSearch = this.handleMapSearch.bind(this);
  }

  componentDidMount(){
    this.props.getAllCountries();
    this.tip=tooltip()
    this.tip.create()
    if (window.innerWidth < 768) {
      this.setState({
        isMobile: true
      })
    }
  }

  handleMouseEnter = (geography, evt, type='world') => {
    let name = type === 'world' ? geography.properties.NAME : geography
    this.tip.show(`
      <div class="tooltip-inner">
        ${name}
      </div>
    `)
    this.tip.position({ pageX: evt.pageX, pageY: evt.pageY })
  }

  handleMouseLeave = () => {
    this.tip.hide()
  }

  handleClick = (e) => {
    if(e.properties.ISO_A2 === "US"){
      this.setState({showUSAMap: true})
    }else{
      this.tip.hide();
      this.props.history.push(`/college-search?country=${e.properties.ISO_A2}&state=&city=&zipcode=&degree=&locale=&tuition_max_val=0&enrollment_min_val=0&enrollment_max_val=0&applicants_min_val=0&applicants_max_val=0&min_reading=&max_reading=&min_sat_math=&max_sat_math=&min_act_composite=&max_act_composite=&type=college&term=&department=&major=null`)
    }

  }

  mapHandler = (slug, ISO_A2, name) => {
    this.tip.hide();
    this.props.history.push(`/college-search?country=&state=${name}&city=&zipcode=&degree=&locale=&tuition_max_val=0&enrollment_min_val=0&enrollment_max_val=0&applicants_min_val=0&applicants_max_val=0&min_reading=&max_reading=&min_sat_math=&max_sat_math=&min_act_composite=&max_act_composite=&type=college&term=&department=&major=null`)
  }

  backHandler = () => {
    this.setState({showUSAMap: false})
  }

  handleCountrySelection(selectedCountry, e) {
    if(selectedCountry.label === 'United States') {
      this.setState({ selectedCountry: selectedCountry, isUSSelected: true });
    } else {
      this.setState({ selectedCountry: selectedCountry, isUSSelected: false, selectedState: '' });
    }
  }

  handleStateSelection(selectedState) {
    this.setState({ selectedState: selectedState });
  }

  handleMapSearch() {
    const { isUSSelected, selectedCountry, selectedState } = this.state;
    if(isUSSelected) {
      this.props.history.push(`/college-search?country=&state=${selectedState.label}&city=&zipcode=&degree=&locale=&tuition_max_val=0&enrollment_min_val=0&enrollment_max_val=0&applicants_min_val=0&applicants_max_val=0&min_reading=&max_reading=&min_sat_math=&max_sat_math=&min_act_composite=&max_act_composite=&type=college&term=&department=&major=null`)
    } else {
      this.props.history.push(`/college-search?country=${selectedCountry.value}&state=&city=&zipcode=&degree=&locale=&tuition_max_val=0&enrollment_min_val=0&enrollment_max_val=0&applicants_min_val=0&applicants_max_val=0&min_reading=&max_reading=&min_sat_math=&max_sat_math=&min_act_composite=&max_act_composite=&type=college&term=&department=&major=null`)
    }
  }

  render(){
    const { countries } = this.props;
    const { isUSSelected, selectedCountry, selectedState } = this.state;

    const searchBtnDisabled = isUSSelected ? !selectedState : !selectedCountry;

    return(
      <div className='search-header'>

          <div className="small-12 use-college-page-title" style={{textAlign: "center"}}>
            <p className="whyCollegePages">Find schools all over the world</p>
          </div>
          {
            !this.state.isMobile &&
            <div>
              {this.state.showUSAMap &&
                <div>
                  <div class="world-map-back" onClick={this.backHandler}>
                    <span class="majors-back-arrow">‹</span> Back
                  </div>
                  <div style={{ textAlign: "center", display: "inline-block", width: "100%"}}>
                    <USAMap defaultFill={"black"} onMouseEnter={this.handleMouseEnter} onMouseLeave={this.handleMouseLeave} onClick={this.mapHandler} />
                  </div>
                </div>}
                {!this.state.showUSAMap && <ComposableMap style={{width: "100%"}}>
                  <Geographies  disableOptimisation={true} geography='https://raw.githubusercontent.com/zcreativelabs/react-simple-maps/master/topojson-maps/world-50m.json'>
                    {(geographies, projection) => geographies.map((geography, i) => {
                      return(
                        <Geography
                          key={i}
                          geography={ geography }
                          projection={ projection }
                          style={{
                            default: {
                                fill: "BLACK",
                                stroke: "WHITE",
                                strokeWidth: 0.75,
                                outline: "none",
                            },
                            hover: {
                                fill: "GREEN",
                                stroke: "WHITE",
                                strokeWidth: 1,
                                outline: "none",
                            },
                            pressed: {
                                fill: "#FF5722",
                                stroke: "#607D8B",
                                strokeWidth: 1,
                                outline: "none",

                            },
                          }}
                          onClick={(e) => this.handleClick(e)}
                          onMouseEnter={this.handleMouseEnter}
                          onMouseLeave={this.handleMouseLeave}
                        />
                    )})}
                  </Geographies>
              </ComposableMap>}
            </div>
          }
          {
            this.state.isMobile && <div className='mobile-map-selection-cont'>
              <Dropdown options={countries.length && countries.map(country => ({ value: country.country_code, label: country.country_name}))} onChange={this.handleCountrySelection} value={selectedCountry.label} placeholder='Select a country' />
              {
                isUSSelected && <Dropdown options={states.length && states.map(state => ({ value: state.key, label: state.value}))} onChange={this.handleStateSelection} value={selectedState.label} placeholder='Select a state' />
              }
              <button className='search-btn' onClick={this.handleMapSearch} disabled={searchBtnDisabled}>Go</button>
            </div>
          }
    </div>
    )
  }
}

const mapStateToProps = state => ({
  countries: state.findColleges.countries,
});

const mapDispatchToProps = dispatch => ({
  getAllCountries: () => { dispatch(getAllCountries()) },
});

export default connect(mapStateToProps, mapDispatchToProps)(withRouter(WorldMap));
