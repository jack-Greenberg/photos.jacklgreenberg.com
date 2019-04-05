const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports =  {
    mode: 'development',
    entry: {
        main: [
            './css/index.scss'
        ],
    },
    output: {
        filename: 'bundle.js',
        path: path.resolve(__dirname, 'build')
    },
    watch: true,
    plugins: [
        new MiniCssExtractPlugin({
            filename: "bundle.css"
        })
    ],
    module: {
        rules: [
            {
                test: /\.(s*)css$/,
                use: [MiniCssExtractPlugin.loader,'css-loader', 'postcss-loader', 'sass-loader']
            },

            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: {
                    loader: "babel-loader"
                }
            }
        ]
    },
};
