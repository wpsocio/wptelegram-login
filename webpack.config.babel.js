import path from 'path';
// import MiniCssExtractPlugin from 'mini-css-extract-plugin';
import autoprefixer from 'autoprefixer';
// import LodashModuleReplacementPlugin from 'lodash-webpack-plugin';
import MiniCssExtractPlugin from 'mini-css-extract-plugin-with-rtl';
// import { BundleAnalyzerPlugin } from 'webpack-bundle-analyzer';
import WebpackRTLPlugin from 'webpack-rtl-plugin';
import camelCase from 'camelCase';

/**
 * Internal dependencies
 */
import { createConfig } from './tools/webpack';

const mainSettings = (isDev) => {
	return createConfig({
		entry: ['regenerator-runtime/runtime', './src/admin/settings/src/index.js'],
		output: {
			path: path.resolve(__dirname, 'src/admin/settings/dist'),
			filename: 'settings-dist.js',
			chunkFilename: '[name].js',
		},
		module: {
			rules: [
				{
					test: /\.css$/, // .less and .css
					use: [
						{
							loader: isDev ? 'style-loader' : MiniCssExtractPlugin.loader,
						},
						{
							loader: 'css-loader',
						},
					],
				},
			],
		},
		plugins: [
			// new BundleAnalyzerPlugin(),
			new MiniCssExtractPlugin({
				filename: 'settings-dist.css',
				chunkFilename: '[name].css',
				rtlEnabled: true,
			}),
			new WebpackRTLPlugin(),
		],
	});
};

const blocks = (isDev) => {
	const settings = mainSettings(isDev);

	const externals = {};
	// Define WordPress dependencies
	const wpPackages = ['block-editor', 'blocks', 'components', 'i18n'];
	// Setup externals for all WordPress dependencies
	wpPackages.forEach((wpPackage) => {
		externals['@wordpress/' + wpPackage] = {
			this: [
				'wp',
				wpPackage.includes('-') ? camelCase(wpPackage) : wpPackage, // 'block-editor' => 'blockEditor'
			],
		};
	});
	return {
		...settings,
		entry: './src/admin/blocks/src/index.js',
		output: {
			path: path.resolve(__dirname, 'src/admin/blocks/dist'),
			filename: 'blocks-build.js',
			libraryTarget: 'this',
		},
		externals: {
			...settings.externals,
			...externals,
		},
		plugins: [
			// new BundleAnalyzerPlugin(),
			new MiniCssExtractPlugin({
				filename: 'blocks-build.css',
				rtlEnabled: true,
			}),
			new WebpackRTLPlugin(),
		],
		module: {
			...settings.module,
			rules: [
				...settings.module.rules,
				{
					test: /\.(sa|sc)ss$/,
					use: [
						{ loader: MiniCssExtractPlugin.loader },
						{ loader: 'css-loader' },
						{
							loader: 'postcss-loader',
							options: {
								ident: 'postcss',
								plugins: [
									autoprefixer({
										flexbox: 'no-2009',
									}),
								],
							},
						},
						{
							loader: 'sass-loader',
							options: {
								prependData: '@import "./src/admin/blocks/src/common.scss";\n',
								sassOptions: {
									outputStyle: 'expanded',
								},
							},
						},
					],
					exclude: /(node_modules|bower_components)/,
				},
			],
		},
	};
};

const configs = (env, argv = {}) => {
	const isDev = 'development' === argv.mode;
	return [mainSettings(isDev), blocks(isDev)];
};

export default configs;
