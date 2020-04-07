const initialState = {
}

const News = (state = initialState, action) => {
  switch(action.type){
    case 'GET_NEWS_SUCCESS':
        return {  ...state,
            newsList: !!state.newsList ? [...state.newsList, ...action.payload.newsdata.data] :  action.payload.newsdata.data, featured_rand_news: action.payload.featured_rand_news, newsMeta: action.payload.newsdata}

    case 'GET_SINGLE_NEWS_SUCCESS':
      return   {...state, singleNews: action.payload    };

    case 'GET_NEWS_FAILURE':
      return   [...state, ...action.payload ];

    case 'RESET_NEWS_DATA':
      return { };

  default:
    return state ;
  }
}

export default News;
