import React from 'react';

export function ProgressBar(props) {
  return (
    <div style={{width: '100%', height: '12px', borderRadius: '2px'}}>
      <div style={{width: `${props.width}%`, backgroundColor: `${props.color}`, height: '100%', borderRadius: '2px', transition: 'all .2s ease-out'}} />
    </div>
  )
}
