// SocialApp/webpack.config.js
var path = require('path');
var webpack = require('webpack');
const Dotenv = require('dotenv-webpack');
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;
var CompressionPlugin = require('compression-webpack-plugin');

module.exports = {
    entry: ["@babel/polyfill", "./index.js"],
    output: {
        filename: "./SocialApp_bundle.js",
        sourceMapFilename: "./SocialApp_bundle.map"
    },
    devtool: '#source-map',
    plugins: [
        new Dotenv(),
        new webpack.DefinePlugin({ // <-- key to reducing React's size
          'process.env': {
            'NODE_ENV': JSON.stringify('production')
          }
        }),
        new webpack.optimize.DedupePlugin(), //dedupe similar code
        new webpack.optimize.UglifyJsPlugin({
            compress: {
                warnings: false,
            },
            mangle: true,
            sourcemap: false,
            debug: false,
            minimize: true,
            compress: {
                warnings: false,
                screw_ie8: true,
                conditionals: true,
                unused: true,
                comparisons: true,
                sequences: true,
                dead_code: true,
                evaluate: true,
                if_return: true,
                join_vars: true
            }
        }), //minify everything
        new webpack.optimize.AggressiveMergingPlugin(),//Merge chunks
        // new BundleAnalyzerPlugin(),
        new webpack.IgnorePlugin(/^\.\/locale$/, [/moment$/]),
        new CompressionPlugin({
            asset: "[path].gz[query]",
            algorithm: "gzip",
            test: /\.js$|\.css$|\.html$/,
            threshold: 10240,
            minRatio: 0.8
        })
    ],
    module: {
        loaders: [
            // jsx compiler - jsx to js
            {
                loader: 'babel',
                test: /\.jsx?$/,
                exclude: /(node_modules|bower_components)/,
                query: {
                    presets: ['react', ['es2015', {loose:true}], 'stage-2']
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
        root: path.resolve('./SocialApp'),
        extenstions: ['', '.js', , '.jsx']
    },
}
