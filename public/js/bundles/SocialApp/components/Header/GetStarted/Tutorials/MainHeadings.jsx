import React from 'react';
import { Card } from './Card.jsx';

export function MainHeadings({ subHeadings, handleCardClick }) {
  return (
    <div>
    {
      !!subHeadings && Object.keys(subHeadings).length && Object.keys(subHeadings).map((text, index) => (
        <Card key={text+index} cardNo={index+1} text={subHeadings[text]} handleCardClick={handleCardClick} />
      ))
    }
    </div>
  )
}
