import React from 'react';

export function DotsLoader(props) {
  const styles = {
    position: 'fixed',
    top: 0,
    right: 0,
    bottom: 0,
    left: 0,
    backgroundColor: 'rgba(0,0,0,0.6)',
    zIndex: '1100',
  }

  const dostsStyles = {
    position: 'absolute',
    top: '50%',
    left: '50%',
    transform: 'translate(-50%, -50%)',
  }

  return (
    <div className="text-center" style={{ ...styles }}>
      <svg width="70" height="20" style={{ ...dostsStyles }}>
        <rect width="17.8948" height="17.8948" x="1.0526" y="1.0526" rx="3" ry="3">
          <animate attributeName="width" values="0;20;20;20;0" dur="1000ms" repeatCount="indefinite"></animate>
          <animate attributeName="height" values="0;20;20;20;0" dur="1000ms" repeatCount="indefinite"></animate>
          <animate attributeName="x" values="10;0;0;0;10" dur="1000ms" repeatCount="indefinite"></animate>
          <animate attributeName="y" values="10;0;0;0;10" dur="1000ms" repeatCount="indefinite"></animate>
        </rect>
        <rect width="1.8948" height="1.8948" x="34.0526" y="9.0526" rx="3" ry="3">
          <animate attributeName="width" values="0;20;20;20;0" begin="200ms" dur="1000ms" repeatCount="indefinite"></animate>
          <animate attributeName="height" values="0;20;20;20;0" begin="200ms" dur="1000ms" repeatCount="indefinite"></animate>
          <animate attributeName="x" values="35;25;25;25;35" begin="200ms" dur="1000ms" repeatCount="indefinite"></animate>
          <animate attributeName="y" values="10;0;0;0;10" begin="200ms" dur="1000ms" repeatCount="indefinite"></animate>
        </rect>
        <rect width="14.1052" height="14.1052" x="52.9474" y="2.9474" rx="3" ry="3">
          <animate attributeName="width" values="0;20;20;20;0" begin="400ms" dur="1000ms" repeatCount="indefinite"></animate>
          <animate attributeName="height" values="0;20;20;20;0" begin="400ms" dur="1000ms" repeatCount="indefinite"></animate>
          <animate attributeName="x" values="60;50;50;50;60" begin="400ms" dur="1000ms" repeatCount="indefinite"></animate>
          <animate attributeName="y" values="10;0;0;0;10" begin="400ms" dur="1000ms" repeatCount="indefinite"></animate>
        </rect>
      </svg>
    </div>
  )
}