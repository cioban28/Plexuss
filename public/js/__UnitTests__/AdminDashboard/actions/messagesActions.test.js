import { Thunk } from 'redux-testkit';

import * as Mactions from './../../../bundles/AdminDashboard/actions/messagesActions';

jest.mock('./../../../bundles/AdminDashboard/actions/messagesActions');


describe("messages Actions", () => {


	beforeEach(() => {
	    jest.resetAllMocks();
	});

	it("tries to load attachments from server", () => {
		console.log('TODO');
	});

});
