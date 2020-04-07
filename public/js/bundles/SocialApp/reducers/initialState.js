const initialState = {
  carousles: {
    isLoading: true,
    hasMoreItems: true,
    comps: [
        'near',
        'ranking',
        'virtual',
        'news',
        'feature',
    ],
    loading: {
        'near': true,
        'ranking': true,
        'virtual': true,
        'news': true,
        'feature': true,
    },
    status: {
        'near': true,
        'ranking': true,
        'virtual': true,
        'news': true,
        'feature': true,
    },
    start: {
        'near': 1,
        'ranking': 1,
        'virtual': 1,
        'news': 1,
        'feature': 1,
    },
    items: {
        'near': true,
        'ranking': false,
        'virtual': false,
        'news': false,
        'feature': false,
    },
    datas: {
        'near': [],
        'ranking': [],
        'virtual': [],
        'news': [],
        'feature': [],
    }
  },
  scholarships: {
    scholarships: [],
    signedIn: 0,
    userId: 0,
    queuedScholarships: [],
    deletedQueuedScholarships: 0,
  },
  posts: {
    posts:[],
    profilePosts:[],
    isNextPost: true,
    socket: '',
    activePostId: '',
    postType: '',
    singlePost: {},
    headerState: false,
    makePost: false,
    sharedPosts: [],
    frndsStateArr: [],
    startPoint: 0,
  },
  messages: {
    currentThreadId: -1,
    friendId: '',
    //one thread data, messages and userInfo
    threadData: [],
    //all messages threads
    messageThreads: {},
    threadInfo: {},
    allThreadMessages: {},
    allThreadUserInfo: {},
    hasNextMessages: {},
    scrollThread: {},
    messagesPageNumber: {},
    isThreads: false,
    unreadThread: 0,

    conversationArr: [],
    showThreadArr: [],
    threadCount: 0,
    nmFlag: false,
    typingMsgArr: [],
    logInUserId: '',
    nextTopicUser: true,
    topicUsrPageNumber: 1,
  },
  modal: {
    isOpen: false,
    isOpenAlumni: false,
  },
  colleges: {
    colleges: [],
    renderManageCollegesIndex: false,
  },
  events: {
    events: [],
  },
  notifications: {
    unread_count: 0,
    notifications: [],
    pageNumber: 1,
    nextNotification: true,
  },
  tutorials: {
    show: false,
    activeHeading: '',
    toggleHeadingChanged: false,
  }
};

export default initialState;
