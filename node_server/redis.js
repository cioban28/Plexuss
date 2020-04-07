// /node_server/redis.js

const Redis = require('ioredis')
const _redisHandlers = require('./redisEventHandlers')

module.exports = () => {
	const redis = new Redis({
		port: process.env.REDIS_PORT || 6379,
		host: process.env.REDIS_HOST || '127.0.0.1',
	});

	redis.on('ready', () => _redisHandlers.ready(redis));

	redis.on('error', () => _redisHandlers.error(redis));

	return redis;
}