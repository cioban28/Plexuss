const initialState = {
  submitting: false,
  successfulSubmission: false,
}

const formSubmission = (state = initialState, action) => {
  switch(action.type) {
    case 'TOGGLE_FORM_SUBMISSION':
      return { ...state, submitting: action.payload };

    case 'TOGGLE_SUCCESSFUL_SUBMISSION':
      return { ...state, successfulSubmission: action.payload };

    default:
      return state;
  }
}

export default formSubmission;
