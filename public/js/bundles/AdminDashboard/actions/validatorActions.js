// validatorActions.js

// just an example - not being used anywhere. Will probably delete later.
export const validateEmail = (val) => {
	var pattern = /[^@]@\.[^\.]/;

	return {
		type: 'EDIT_EMAIL',
		payload: {
			email_valid: pattern.test(val),
			msg: 'Email is invalid. Ex: university@email.com'
		}
	};
};

// profile validation 
export const profileFormValid = (bool) => {
	return {
		type: 'PROFILE_FORM_VALID',
		payload: {profileFormValid: bool}
	};
}

// profile permission validation 
export const profilePermissionsFormValid = (bool) => {
	return {
		type: 'PROFILE_PERMISSIONS_FORM_VALID',
		payload: {profilePermissionsFormValid: bool}
	};
}