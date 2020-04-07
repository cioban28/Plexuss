// /node_server/redisEventHandlers.js

const _print = data => console.log(data);

module.exports = {

	config: {
	    port: process.env.REDIS_PORT || 6379,
	    host: process.env.REDIS_HOST || '127.0.0.1',
	},

	ready: (redis) => {
        console.log('redis ready');

        var x = redis.keys('*');
        var _user = redis.hgetall('user:9160');

        x.then(_print);
        _user.then(_print);
    },

	error: err => _print('redis error'),

}