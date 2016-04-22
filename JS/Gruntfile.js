var webpack = require('webpack');

module.exports = function(grunt) {

	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		babel: {
			options: {
				sourceMap: true,
				presets: ['babel-preset-es2015'],
			},
			build: {
				cwd: 'model',
				src: ['*.js'],
				dest: 'build/model',
				expand: true,
			},
		},
		webpack: {
			build: {
				progress: true,
				entry: './build/model/pantheum.js',
				output: {path:'build/',filename:'bundle.js'},
				module: {
					loaders: [
						{ test: /\.json$/, loader: 'json-loader' },
					],
				},
				plugins: [
					new webpack.IgnorePlugin(/^(cls-bluebird)$/),
				],
				externals: {
					fs: '{}',
					tls: '{}',
					net: '{}',
				},
			}
		},
		browserify: {
			options: {
				ignore: ['cls-bluebird'],
			},
			build: {
				src: 'build/model/pantheum.js',
				dest: 'build/model.js',
			},
		},
		uglify: {
			options: {
				banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n',
			},
			build: {
				files: {
					'build/model.min.js': 'build/model.js',
				},
			},
		},
	});

	grunt.loadNpmTasks('grunt-webpack-without-server');
	grunt.loadNpmTasks('grunt-browserify');
	grunt.loadNpmTasks('grunt-babel');
	grunt.loadNpmTasks('grunt-contrib-uglify');

	// Default task(s).
	grunt.registerTask('default', ['babel', 'browserify', 'uglify']);

};
