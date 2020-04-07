import React, { Component } from 'react'
import SearchedCollegesListItem from './SearchedCollegesListItem';

class CollegesSearchList extends Component{
  constructor(props){
    super(props)
  }

  render(){
    return( 
      <div className='colleges-table'>
        <div id = '_sortingBar' className =' sic_on'>
          <div className='sort-bar'>
            <div className='col col-1 rank '>
              <div className='inner-col'>
                <div className='sort-arrows'>
                  <div className='arrow up'></div>
                  <div className='arrow down'></div>
                </div>
                <span className="name ">Rank</span>
              </div>	
            </div>
            <div className='col col-4 ' style={{textAlign: 'center'}}>
              <div className='inner-col'>
                <div className='sort-arrows'>
                  <div className='arrow up'></div>
                  <div className='arrow down'></div>
                </div>
                <span className="name ">School</span>
              </div>	
            </div>
            <div className='col col-3 '>
              <div className='inner-col'>
                <div className='sort-arrows'>
                  <div className='arrow up'></div>
                  <div className='arrow down'></div>
                </div>
                <span className="name ">Location</span>
              </div>	
            </div>
            <div className='col col-2 '>
              <div className='inner-col'>
                <div className='sort-arrows'>
                  <div className='arrow up'></div>
                  <div className='arrow down'></div>
                </div>
                <span className="name ">Application cost</span>
              </div>	
            </div>
            <div className='col col-2 ' style={{textAlign: 'center'}}>
              <div className='inner-col'>
                <div className='sort-arrows'>
                  <div className='arrow up'></div>
                  <div className='arrow down'></div>
                </div>
                <span className="name ">Add</span>
              </div>	
            </div>
          </div>
        </div>

        {this.props.colleges && this.props.colleges.map((college, index) =>  <SearchedCollegesListItem key={index} college={college} /> )}
      </div>
    )
  }
}

export default CollegesSearchList;