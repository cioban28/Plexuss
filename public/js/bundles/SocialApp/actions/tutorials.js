export function showTutorials(payload) {
  return {
    type: 'SHOW_TUTORIALS',
    payload,
  }
};

export function hideTutorials(payload) {
  return {
    type: 'HIDE_TUTORIALS',
    payload,
  }
};

export function setActiveHeading(payload) {
  return {
    type: 'SET_ACTIVE_HEADING',
    payload,
  }
}
