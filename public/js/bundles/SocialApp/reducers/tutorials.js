import initialState from './initialState';

const tutorials = (state = initialState.tutorials, action) => {
  switch(action.type) {
    case 'SHOW_TUTORIALS':
      return { ...state, show: true };

    case 'HIDE_TUTORIALS':
      return { ...state, show: false, activeHeading: '' };

    case 'SET_ACTIVE_HEADING':
      return { ...state, activeHeading: action.payload, toggleHeadingChanged: !state.toggleHeadingChanged };

    default:
      return state;
  }
}

export default tutorials;
