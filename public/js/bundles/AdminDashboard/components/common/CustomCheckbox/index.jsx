import React from 'react';
import './styles.scss';


export function CustomCheckbox(props) {
  return (
    <label className={`custom-checkbox-cont ${props.styles}`}>
      <input type='checkbox' checked={props.checked} onClick={props.handleClick} />
      <span className='checkmark'></span>
    </label>
  )
}
