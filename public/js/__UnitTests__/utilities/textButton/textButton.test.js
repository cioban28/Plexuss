import React from 'react';
import TextButton from './../../../bundles/utilities/textButton/textButton';
import renderer from 'react-test-renderer';

describe('TextButton', () => {

  it('button has active class when active prop passed', () => {
      const component = renderer.create(
        <TextButton title="Facebook" active="Facebook"></TextButton>
      );

      let tree = component.toJSON();
      expect(tree).toMatchSnapshot();
  });



  it('button not active', () => {
      const component = renderer.create(
        <TextButton title="Facebook" active="Google"></TextButton>
      );

      let tree = component.toJSON();
      expect(tree).toMatchSnapshot();
  });

  
});

