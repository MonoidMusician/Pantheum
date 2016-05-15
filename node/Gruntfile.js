module.exports = function(grunt) {
	require('time-grunt')(grunt);

	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		babel: {
			options: {
				sourceMap: true,
				presets: ['babel-preset-es2015'],
			},
			build: {
				expand: true,
				src: 'model react languages user'.split(' ').map(d=>d+'/*.js').concat('pantheum.js'),
				dest: 'build/',
			},
		},
		browserify: {
			options: {
				ignore: ['cls-bluebird'],
			},
			build: {
				files: {
					'build/browser.js': 'build/pantheum.js',
				}
			},
		},
		uglify: {
			options: {
				banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n',
			},
			build: {
				files: {
					'build/bundle.js': 'build/browser.js',
				},
			},
		},
	});

	grunt.loadNpmTasks('grunt-browserify');
	grunt.loadNpmTasks('grunt-babel');
	grunt.loadNpmTasks('grunt-contrib-uglify');

	// Default task(s).
	grunt.registerTask('default', ['babel', 'browserify', 'uglify']);

};
