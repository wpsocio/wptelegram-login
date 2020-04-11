/**
 * Gulp Configuration File
 */
/**
 * Internal dependencies
 */
import pkg from './package.json';

const srcDir = './src';
const name = pkg.name;

const config = {
	srcDir,
	buildDir: './build',
	watchPhp: srcDir + '/**/*.php',
	vendorBin: './vendor/bin',
	PhpStandard: 'WordPress',
	ESNextJS: srcDir + '/**/*.esnext.js',

	// Translation options.
	textDomain: name,
	potFilename: `${ name }.pot`,
	JSPotFilename: `${ name }.js.pot`,
	domainPath: srcDir + '/languages',
	packageName: 'WP Telegram Comments',
	bugReport: 'http://wordpress.org/support/plugin/' + name,
	lastTranslator: 'Manzoor Wani <@manzoorwanijk>',
	team: 'WP Telegram Team',
	BROWSERS_LIST: [
		'last 2 version',
		'> 1%',
		'ie >= 11',
		'last 1 Android versions',
		'last 1 ChromeAndroid versions',
		'last 2 Chrome versions',
		'last 2 Firefox versions',
		'last 2 Safari versions',
		'last 2 iOS versions',
		'last 2 Edge versions',
		'last 2 Opera versions'
	]
};

export default config;
