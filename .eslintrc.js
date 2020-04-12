module.exports = {
	env: {
		browser: true,
		commonjs: true,
		es6: true,
		node: true,
	},
	extends: ['plugin:react/recommended', 'eslint:recommended', 'plugin:@wordpress/eslint-plugin/recommended'],
	parserOptions: {
		sourceType: 'module',
		allowImportExportEverywhere: true,
		codeFrame: true,
		ecmaFeatures: {
			templateStrings: true,
		},
		ecmaVersion: 2018,
	},
	parser: 'babel-eslint',
	rules: {
		'react/prop-types': 'off',
		'comma-dangle': 'off',
		indent: ['error', 'tab'],
		'linebreak-style': ['error', 'unix'],
		"quotes": [2, "single", { "avoidEscape": true }],
		semi: ['error', 'always'],
		curly: 'warn',
		'no-mixed-spaces-and-tabs': 'warn',
		indent: [
			'error',
			'tab',
			{
				SwitchCase: 1,
			},
		],
		camelcase: 'off',
		'no-console': 'off',
		'no-alert': 'off',
		'no-var': 'off',
		'vars-on-top': 'off',
		'lines-around-comment': 'off',
	},
	plugins: ['eslint-plugin-react'],
	globals: {
		jQuery: 'readonly',
	},
};
