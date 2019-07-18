const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const autoprefixer = require( 'autoprefixer' );
const LodashModuleReplacementPlugin = require('lodash-webpack-plugin');
// Configuration for the MiniCssExtractPlugin
const BlocksCssExtractConfig = [
  { loader: MiniCssExtractPlugin.loader },
  { loader: 'css-loader' },
  {
    loader: 'postcss-loader',
    options: {
      ident: 'postcss',
      plugins: [
        autoprefixer( {
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
const SettingsCssExtractConfig = [
  { loader: MiniCssExtractPlugin.loader },
  { loader: 'css-loader' },
  {
    loader: 'postcss-loader',
    options: {
      ident: 'postcss',
      plugins: [
        autoprefixer( {
          flexbox: 'no-2009',
        } ),
      ],
    },
  },
  { loader: 'sass-loader' },
];

module.exports.blocks = {
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
        use: BlocksCssExtractConfig,
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

module.exports.settings = {
  mode: 'production',
  entry: './src/admin/settings/index.js',
  output: {
    path: path.resolve(__dirname, 'build/admin/settings'),
    filename: 'settings-build.js'
  },
  optimization: {
    minimize: false
  },
  devtool: false,
  module: {
    rules: [
      {
        test: /\.js$/,
        use: {
          loader: 'babel-loader',
          options: {
            plugins: ['@babel/plugin-transform-react-jsx']
          },
        },
        exclude: /(node_modules|bower_components)/,
      },
      {
        test: /\.(sa|sc|c)ss$/,
        use: SettingsCssExtractConfig,
        exclude: /(node_modules|bower_components)/
      }
    ]
  },
  plugins: [
    new LodashModuleReplacementPlugin(),
    new MiniCssExtractPlugin({
      filename: 'settings-build.css'
    }),
  ]
};