// cmsLogoActions.js

import $ from 'jquery'
import axios from 'axios'

export const saveLogo = (form) => {
	return (dispatch) => {
		dispatch({
	 		type: 'CMS_PENDING',
	 		payload: {pending: true}
	 	});

	 	$.ajax({
			url: '/admin/ajax/saveLogo',
			type: 'POST',
			data: form,
			enctype: 'multipart/form-data',
			contentType: false,
        	processData: false,
        	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        	success: (data) => {
				dispatch({
			 		type: 'SAVE_LOGO_DONE',
			 		payload: {
			 			pending: false,
			 		}
			 	});
        	},
        	error: (err) => {
				dispatch({
			 		type: 'CMS_PENDING',
			 		payload: {pending: true}
			 	});
        	}
		});
	}
}

export const uploadLogo = (uploadedFile) => {
	return {
		type: 'UPLOAD_LOGO',
		payload: {
			file: uploadedFile,
			fileURL: URL.createObjectURL(uploadedFile),
		}
	}
}