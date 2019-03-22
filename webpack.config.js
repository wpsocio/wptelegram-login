const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const autoprefixer = require( 'autoprefixer' );

// Configuration for the MiniCssExtractPlugin
const CssExtractConfig = [
  { loader: MiniCssExtractPlugin.loader },
  { loader: 'css-loader' },
  {
    loader: 'postcss-loader',
    options: {
      ident: 'postcss',
      plugins: [
        autoprefixer( {
          browsers: [
            '>1%',
            'last 4 versions',
            'Firefox ESR',
            'not ie < 9', // React doesn't support IE8
          ],
          flexbox: 'no-2009',
        } ),
      ],
    },
  },
  {
    loader: 'sass-loader',
    options: {
      data: '@import "./src/admin/blocks/common.scss";\n',
      outputStyle: 'expanded',
    },
  },
];

module.exports = {
  mode: 'production',
  entry: './src/admin/blocks/index.js',
  output: {
    path: path.resolve(__dirname, 'build/admin/blocks'),
    filename: 'blocks-build.js'
  },
  optimization: {
    minimize: false
  },
  devtool: false,
  module: {
    rules: [
      {
        test: /\.js$/,
        use: 'babel-loader',
        exclude: /(node_modules|bower_components)/,
      },
      {
        test: /\.(sa|sc|c)ss$/,
        use: CssExtractConfig,
        exclude: /(node_modules|bower_components)/
      }
    ]
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: 'blocks-build.css'
    })
  ]
};