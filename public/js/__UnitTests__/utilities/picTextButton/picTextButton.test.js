import React from 'react';
// import PicTextButton from './../../../bundles/utilities/picTextButton/picTextButton';
import PicTextButton from './../../../bundles/utilities/picTextButton/picTextButton';
import renderer from 'react-test-renderer';

describe('PicTextButton', () => {

  it('pic button render test', () => {
      const component = renderer.create(
        <PicTextButton text="Facebook" imageClass="imgClass"></PicTextButton>
      );

      let tree = component.toJSON();
      expect(tree).toMatchSnapshot();
  });


}); 