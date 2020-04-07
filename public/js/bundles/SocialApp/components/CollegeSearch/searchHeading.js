
import React from 'react';
import './styles.scss'
 
const SearchHeading = (props) => (
    <div className="search-headning-div">       
        We’ve found <span className="f-bold fs22 c98 recordCount">{props.recordCount}</span> results for <span className="term-searched">colleges in {props.stateName}</span>...
    </div>
)


export default SearchHeading