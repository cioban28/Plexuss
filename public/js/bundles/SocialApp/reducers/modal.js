import * as types from '../actions/actionTypes';
import initialState from './initialState';

export const modal = (state = initialState.modal, action) => {
  switch(action.type) {
    case types.OPEN_MODAL:
      return { ...state, isOpen: true };

    case types.CLOSE_MODAL:
      return { ...state, isOpen: false };

    case types.OPEN_MODAL_ALUMNI:
      return { ...state, isOpenAlumni: true };

    case types.CLOSE_MODAL_ALUMNI:
      return { ...state, isOpenAlumni: false };

    default:
      return state;
  }
};
