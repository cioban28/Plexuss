import React from 'react';

export function CustomStatusCircle(props) {
  const styles = {
    display: 'inline-block',
    width: '10px',
    height: '10px',
    borderRadius: '50%',
    border: `${props.border}`,
    backgroundColor: `${props.color}`,
  }

  return (
    <span style={styles} />
  )
}
