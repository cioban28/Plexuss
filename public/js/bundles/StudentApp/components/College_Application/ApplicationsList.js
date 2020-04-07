import React, { Component } from 'react'
import ApplicationsListItem from './ApplicationsListItem';

class ApplicationsList extends Component{
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
                    <div className='col col-4 ' style={{width: '30%', textAlign: 'center'}} >
                        <div className='inner-col'>
                            <div className='sort-arrows'>
                                <div className='arrow up'></div>
                                <div className='arrow down'></div>
                            </div>
                            <span className="name ">School</span>
                        </div>    
                    </div>
                    <div className='col col-2 '>
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
                    <div className='col col-2 '>
                        <div className='inner-col'>
                            <div className='sort-arrows'>
                                <div className='arrow up'></div>
                                <div className='arrow down'></div>
                            </div>
                            <span className="name ">Application Link</span>
                        </div>    
                    </div>
                    <div className='col col-1 '>
                        <div className='inner-col'>
                            <div className='sort-arrows'>
                                <div className='arrow up'></div>
                                <div className='arrow down'></div>
                            </div>
                            <span className="name ">Remove</span>
                        </div>    
                    </div>
                </div>
            </div>

            {!!this.props.myApplicationsList && this.props.myApplicationsList.map((college, index) => <ApplicationsListItem key={index} college={college} />)}


        </div>

         )

 }
}

export default ApplicationsList