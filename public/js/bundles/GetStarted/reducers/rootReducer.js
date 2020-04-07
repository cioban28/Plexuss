import { combineReducers } from 'redux';
import {reducer as toastrReducer} from 'react-redux-toastr'
import steps from './step'
import _user from './user'

const rootReducer = combineReducers({
  toastr: toastrReducer,
  steps: steps,
  _user: _user,
});

export default rootReducer;
