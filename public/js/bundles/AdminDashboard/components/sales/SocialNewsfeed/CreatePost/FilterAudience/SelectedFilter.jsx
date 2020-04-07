import React from 'react';

export function SelectedFilter(props) {
  return (
     <span className={props.selectedListUnderFilter ? 'selected-filter-cont-white-bg' : 'selected-filter-cont'}>
      { props.include && <span className='include'>+</span> }
      { props.exclude && <span className='exclude'>-</span> }
      <p className='filter-name'>{ props.filterName }</p>
      { !props.noRemoveBtn &&<span className='remove' onClick={props.handleRemoveFilter.bind(this, props.filter, props.filterName)}>x</span> }
    </span>
  )
}
