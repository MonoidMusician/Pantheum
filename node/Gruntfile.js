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
				src: 'model react user lib'.split(' ').map(d=>d+'/*.js').concat('languages/**/*.js', 'pantheum.js', 'la_ipa.js'),
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
			devel: {
				files: {
					'build/browser.js': 'pantheum.js',
				},
			},
			develive: {
				options: {
					watch: true,
					keepAlive: true,
				},
				files: {
					'build/browser.js': 'pantheum.js',
				},
			},
			static: {
				files: {
					'build/static.js': 'static.js',
				},
			},
			develite: {
				options: {
					watch: true,
					keepAlive: true,
					transform: [
						['exposify', {expose:{
							'react': 'window.React',
							'react-dom': 'window.ReactDOM',
							'react-hyperscript': 'window.h',
							'material-ui': 'window.MaterialUI',
							'material-ui/svg-icons': 'window.MaterialUI.svgicons',
							'material-ui/styles': 'window.MaterialUI.styles',
						}}]
					],
				},
				files: {
					'build/pantheum.js': 'pantheum.js',
				},
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
		watch: {
			devel: {
				tasks: 'devel',
				files: 'model react user lib'.split(' ').map(d=>d+'/*.js').concat('languages/**/*.js', 'pantheum.js', 'la_ipa.js'),
			}
		}
	});

	grunt.loadNpmTasks('grunt-browserify');
	grunt.loadNpmTasks('grunt-babel');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-watch');

	// Default task(s).
	grunt.registerTask('default', ['babel:build', 'browserify:build', 'uglify']);
	grunt.registerTask('quick', ['babel:build', 'browserify:build']);
	grunt.registerTask('devel', 'browserify:devel');
	grunt.registerTask('develive', 'browserify:develive');
	grunt.registerTask('static', 'browserify:static');
	grunt.registerTask('develite', 'browserify:develite');
};
