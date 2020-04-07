import React from 'react';
import {shallow, mount, render} from 'enzyme';
import {Provider} from 'react-redux';
import configureMockStore from 'redux-mock-store';
import thunk from 'redux-thunk';
import renderer from 'react-test-renderer';
import toJson from 'enzyme-to-json';

import AttachmentsButton   from './../../../bundles/utilities/AttachmentsModal/attachmentsButton';
import AttachmentsModal   from './../../../bundles/utilities/AttachmentsModal/components/attachmentsModal';


const middlewares = [thunk];
const mockStore = configureMockStore(middlewares);



describe('AttachmentsModal', () => {


	it('modal button renders', () => {
		const wrapper = render(
			<AttachmentsButton store={mockStore({ messages: {} })} />
		);

	    expect(toJson(wrapper)).toMatchSnapshot();
	});



	it('modal button toggles modal', () => {
		const wrapper = mount(
			<Provider  store={mockStore({ messages: {} })} >
				<AttachmentsButton />
			</Provider>
		);

		let tree = toJson(wrapper);
	    wrapper.find('._picTextButton').simulate('click');

	    //console.log(tree);
	    expect(wrapper.find('._AttachmentModal').length).toEqual(1);
	    expect(tree).toMatchSnapshot();


	});

});

