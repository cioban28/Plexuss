import React from 'react';
import { Card } from './Card.jsx';

export function SubHeadingsHOC({ subHeadings, handleShowTutorials, activeHeading }) {
  return (
    <div>
    {
      !!subHeadings && Object.keys(subHeadings).length && Object.keys(subHeadings).map((text, index) => (
        <Card key={text+index} text={subHeadings[text]} handleShowTutorials={handleShowTutorials} activeHeading={activeHeading} />
      ))
    }
    </div>
  )
}
