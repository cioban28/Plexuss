const initialState ={
  isOpen: false,
}

const slidingMenu = (state = initialState, action) => {
  switch(action.type){
    case 'TOGGLE_SLIDING_MENU':
      return { ...state, isOpen: !state.isOpen };
    default:
      return state;
  }
}

export default slidingMenu;
