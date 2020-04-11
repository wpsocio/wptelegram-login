import UglifyJsPlugin from 'uglifyjs-webpack-plugin';
import OptimizeCSSAssetsPlugin from 'optimize-css-assets-webpack-plugin';

export const createConfig = (options) => {
	const { module: { rules = [] } = {}, optimization = {}, ...rest } = options;

	const config = {
		mode: 'production',
		cache: false,
		optimization: {
			minimize: true,
			minimizer: [
				new UglifyJsPlugin({
					uglifyOptions: {
						output: {
							comments: false,
						},
					},
				}),
				new OptimizeCSSAssetsPlugin({}),
			],
			...optimization,
		},
		module: {
			rules: [
				{
					test: /\.jsx?$/,
					use: {
						loader: 'babel-loader',
						options: {
							// plugins: [ '@babel/plugin-transform-react-jsx' ],
						},
					},
					exclude: /(node_modules|bower_components)/,
				},
			].concat(rules),
		},
		externals: {
			jquery: 'jQuery',
			'plugin-data': 'wptelegram_login',
		},
		...rest,
	};

	return config;
};
