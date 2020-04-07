var path = require('path');
var webpack = require('webpack');
var visualizer = require('webpack-visualizer-plugin');
// var babelJest = require('babel-jest');

// require('./../../../../node_env.js'); // import env variables

module.exports = {
    entry: "./components/AdminDashboard_App.js",
    output: {
        filename: "./AdminDashboard_bundle.js",
        sourceMapFilename: "./AdminDashboard_bundle.map"
    },
    devtool: '#source-map',
    plugins: [
        new visualizer(),
        // new webpack.optimize.UglifyJsPlugin({minimize: true}),
        // new webpack.DefinePlugin({
        //     'process.env': {
        //         PRODUCTION: JSON.stringify(process.env.PRODUCTION),
        //         SOCKETIO_CLIENT_DOMAIN: JSON.stringify(process.env.SOCKETIO_CLIENT_DOMAIN),
        //     }
        // }),
    ],
    module: {
        loaders: [
            // jsx compiler - jsx to js
            {
                loader: 'babel',
                test: /\.jsx?$/,
                exclude: /(node_modules|bower_components)/,
                query: {
                    presets: ['react', 'es2015', 'stage-2']
                }
            },

            // sass loader/sourcemapping - sass to css
            {
                test: /\.scss$/,
                loaders: [
                    'style',
                    'css',
                    'autoprefixer?browsers=last 3 versions',
                    'sass?outputStyle=expanded'
                ] //adding sourcemapping to css/sass loaders
            },

            // less loader - less to css
            {
                test: /\.less$/,
                loader: "css-loader!less-loader"
            },

            // css loader -- ended up needing to import css for react-datepicker
            {
              test: /\.css$/,
              loader: 'style-loader'
            }, {
              test: /\.css$/,
              loader: 'css-loader',
              query: {
                modules: true,
                localIdentName: '[name]__[local]___[hash:base64:5]'
              }
            },

            // img loader/optimization
            {
                test: /\.(jpe?g|png|gif|svg)$/i,
                loaders: [
                    'url?limit=8192',
                    'img'
                ]
            },
        ]
    },
    sassLoader: {
        includePaths: ['client/style'], //makes calling styles not relative
    },
    resolve: {
        root: path.resolve('./AdminDashboard'),
        extenstions: ['', '.js', '.jsx']
    },

};
