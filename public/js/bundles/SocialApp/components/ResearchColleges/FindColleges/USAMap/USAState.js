import React from "react";

const USAState = (props) => {
  return (
    <path onMouseLeave={(e) => props.onMouseLeave(props.name)} onMouseEnter={(e) => props.onMouseEnter(props.name, e, 'USA')} d={props.dimensions} fill={props.fill} data-name={props.state} className={`${props.state} state`} onClick={() => props.onClick(props.slug, props.ISO_A2, props.name)} />
  );
}
export default USAState;