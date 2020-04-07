import React, { Component } from 'react'
import Autocomplete from 'react-autocomplete';
import './styles.scss'
import axios from 'axios';

class BattleColleges extends Component{
  constructor(props){
    super(props)
      this.state = {
        firstInputAutoComplete: [],
        secondInputAutoComplete: [],
        thirdInputAutoComplete: [],
        first: '',
        second: '',
        third: ''
    }
  }

  _onChangeCollege1 = (event, type) => {
    switch(type){
      case 'first':
        this.setState({first: {label: event.target.value}})
        break;
      case 'second':
        this.setState({second: {label: event.target.value}})
        break;
      case 'third':
        this.setState({third: {label: event.target.value}})
        break;
      default: break;
    }


    if(event.target.value.length > 0){
      axios({
        method:'GET',
        url: `/getslugAutoCompleteData?type=colleges&term=${event.target.value}`,
      })
      .then(res => {
        switch(type){
          case 'first':
            this.setState({firstInputAutoComplete: res.data})
            break;
          case 'second':
            this.setState({secondInputAutoComplete: res.data})
            break;
          case 'third':
            this.setState({thirdInputAutoComplete: res.data})
            break;
          default: break;
        }
      })
      .catch(error => {
        this.setState({firstInputAutoComplete: [], secondInputAutoComplete: [], thirdInputAutoComplete: []})
        
      })
    }
    else{
      this.setState({
        firstInputAutoComplete: [],
        secondInputAutoComplete: [],
        thirdInputAutoComplete: []
      })
    }
  }

  handleListItemClick = (college, type) => {
    this.setState( (prevState) => ({
      firstInputAutoComplete: [],
      secondInputAutoComplete: [],
      thirdInputAutoComplete: [],
      first: type === 'first' ? college : prevState.first,
      second: type === 'second' ? college : prevState.second,
      third: type === 'third' ? college : prevState.third,
    }))
  }

  componentDidMount(){
  }

  render(){
    let colleges = Object.assign([],this.props.colleges)
    return(
      <div className="box-div" id="compare-box-div" style={{position: "absolute", left: "0px; top: 0px"}}>
        <div id="comparebox_content_div">
          <div className="header-banner" style={{backgroundColor: "#04a6ae"}}>Compare Colleges</div>
          
          <div className="banner-content-div" style={{backgroundColor: "#03747a"}}>
            <div className="college-compare-outer">
              <div className="college-compare-head1" style={{textAlign: "center"}}>
                <span style={{fontWeight: "bold"}}>COMPARE</span> COLLEGES
              </div>
              
              <div className="college-compare-head2 small-text-center">COMPARE THE TOP STATS OF ANY COLLEGES</div>
              
              <div className="college-compare-div-first ui-front large-12">


                <input
                  id="collegeAutoComplete1"
                  placeholder="Start typing a college name..."
                  className="search-text-box ui-autocomplete-input no-margin-bottom"
                  name="collegeAutoComplete1" 
                  type="text"
                  value={(this.state.first && this.state.first.label) || '' }
                  autoComplete="off"
                  onChange={(e) => this._onChangeCollege1(e, 'first')} />

                
                <input id="collegeAutoCompleteId1" name="collegeAutoCompleteId1" type="hidden" />

                <div style={{position: "absolute", fontSize: "20px", color: "#797979", fontWeight: "bold", top: "23px", right: "15px"}}>1</div>

                { this.state.firstInputAutoComplete.length > 0 &&
                  <ul className="ui-autocomplete ui-front ui-menu ui-widget ui-widget-content large-12 " id="ui-id-4" tabIndex="0" style={{float: "left", width: "100%"}}>
                    { this.state.firstInputAutoComplete.length > 0 && 
                      this.state.firstInputAutoComplete.map( (datum, index) => <li key={index} onClick={ () => this.handleListItemClick(datum, 'first') } className="ui-menu-item" id="ui-id-7" style={{cursor: "pointer"}} tabIndex="-1">
                        {datum.label}
                      </li>
                      )}
                  </ul>
                }
              </div>

              <div className="college-compare-div-other college-compare-div-second ui-front large-12">
                <input 
                  id="collegeAutoComplete2" 
                  placeholder="Start typing a college name..." 
                  className="search-text-box ui-autocomplete-input" 
                  name="collegeAutoComplete2" type="text" 
                  autoComplete="off"
                  style={{margin: "0px"}}
                  onChange={(e) => this._onChangeCollege1(e, 'second')}
                  value={(this.state.second && this.state.second.label) || '' }
                />
                <input id="collegeAutoCompleteId2" name="collegeAutoCompleteId2" type="hidden" />
                <div className="college-compare-right-other">2</div>
                { this.state.secondInputAutoComplete.length > 0 && 
                  <ul className="ui-autocomplete ui-front ui-menu ui-widget ui-widget-content large-12 " id="ui-id-5" tabIndex="0" style={{float: "left", width: "100%"}}>
                  { 
                    this.state.secondInputAutoComplete.length > 0 && 
                      this.state.secondInputAutoComplete.map( (datum, index) => <li key={index} onClick={ () => this.handleListItemClick(datum, 'second') } className="ui-menu-item" id="ui-id-7" style={{cursor: "pointer"}} tabIndex="-1">
                        {datum.label}
                      </li>
                      )}
                  </ul>
                }
              </div>

              <div className="college-compare-div-other college-compare-div-third ui-front large-12">
                <input 
                  id="collegeAutoComplete3" 
                  placeholder="Start typing a college name..." 
                  className="search-text-box ui-autocomplete-input" 
                  name="collegeAutoComplete3" 
                  type="text"
                  autoComplete="off"
                  style={{margin: 0}}
                  onChange={(e) => this._onChangeCollege1(e, 'third')}
                  value={(this.state.third && this.state.third.label) || '' }
                />
                
                <input id="collegeAutoCompleteId3" name="collegeAutoCompleteId3" type="hidden"/>
                <div className="college-compare-right-other">3</div>
                { this.state.thirdInputAutoComplete.length > 0 &&   
                  <ul className="ui-autocomplete ui-front ui-menu ui-widget ui-widget-content large-12 " id="ui-id-6" tabIndex="0" style={{float: "left", width: "100%"}}>
                    { 
                      this.state.thirdInputAutoComplete.length > 0 && 
                        this.state.thirdInputAutoComplete.map( (datum, index) => <li key={index} onClick={ () => this.handleListItemClick(datum, 'third') } className="ui-menu-item" id="ui-id-7" style={{cursor: "pointer"}} tabIndex="-1">
                          {datum.label}
                        </li>
                    )}
                  </ul>

                
                }
              </div>

              <div style={{textAlign: "center", marginTop: "2%"}} className="footer-banner college-compare-comparebox">
                <div >
                  <a href={`https://plexuss.com/comparison/?UrlSlugs=${this.state.first && this.state.first.slug},${this.state.second && this.state.second.slug},${this.state.third && this.state.third.slug} `}>
                    <img alt="batlleimage" src="/images/colleges/battle.png" /> &nbsp; <span className="battlefont f-normal">Battle !</span>
                  </a>
                </div>
              </div>

              <div style={{height: "26px"}}>&nbsp;</div>
            </div>
          </div>
        </div>
      </div>
    )
  }
}

export default BattleColleges;