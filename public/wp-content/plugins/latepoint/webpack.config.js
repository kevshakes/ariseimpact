const path = require('path');
var webpack = require('webpack');

module.exports = {
	mode: 'none',
  entry: {
  	'sprintf' : './node_modules/sprintf-js/dist/sprintf.min.js',
  	'perfect-scrollbar' : './node_modules/perfect-scrollbar/dist/perfect-scrollbar.min.js',
		'dragula' : './node_modules/dragula/dist/dragula.min.js',
		'Chart' : './node_modules/chart.js/dist/Chart.min.js',
		'moment' : './node_modules/moment/min/moment-with-locales.min.js',
		'jquery.inputmask.bundle' : './node_modules/inputmask/dist/min/jquery.inputmask.bundle.min.js',
		'daterangepicker' : './node_modules/daterangepicker/daterangepicker.js',
		'pickr' : './node_modules/pickr-widget/dist/pickr.min.js',
  },
  output: {
    filename: '[name].min.js',
    path: path.resolve(__dirname, 'public', 'javascripts', 'vendor'),
  },
	plugins: [
	new webpack.ProvidePlugin({
	      moment: "moment"
	    })
	]
};