/*!
 * Bootstrap's Gruntfile
 * https://getbootstrap.com
 * Copyright 2013-2016 The Bootstrap Authors
 * Copyright 2013-2016 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 */

module.exports = function (grunt) {

	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		watch: {
			sass: {
				files: 'src/scss/*.scss',
				tasks: ['css']
			},
			js: {
				files: ['src/*.js','src/*.html'],
				tasks: ['js']
			},
			livereload: {
				files: ['dist/*'],
				options: {
					livereload: true
				}
			}
		},

		sass: {
			main: {
				files: {
					'dist/css/ebooks_style.css': 'src/scss/bootstrap.scss'
				}
			}
		},

		cssmin: {
			build: {
				src: 'dist/css/ebooks_style.css',
				dest: 'dist/css/ebooks_style.min.css'
			}
		},

		concat: {
			options: {
				separator: '\n/*next file*/\n\n'  //this will be put between conc. files
			},
			dist: {
				src: ['bootstrap.min.js','build/*.js', 'src/main.js'],
				dest: 'dist/js/app.js'
			}
		},

		uglify: {
			options: {
				mangle: true,
				sourceMap: true,
				banner: '/*! <%= pkg.name %> - v<%= pkg.version %> - ' +
				'<%= grunt.template.today("yyyy-mm-dd") %> */',
				//compress: true

			},
			build: {
				src: [
					/* Required for bootstrap */
					'node_modules/jquery/dist/jquery.js',
					'node_modules/tether/dist/tether.js',
					/* Bootstrap scripts */
					'node_modules/bootstrap/dist/js/dist/util.js',
					'node_modules/bootstrap/dist/js/dist/alert.js',
					'node_modules/bootstrap/dist/js/dist/button.js',
					//'node_modules/bootstrap/dist/js/dist/carousel.js',
					//'node_modules/bootstrap/dist/js/dist/collapse.js',
					'node_modules/bootstrap/dist/js/dist/dropdown.js',
					'node_modules/bootstrap/dist/js/dist/modal.js',
					//'node_modules/bootstrap/dist/js/dist/scrollspy.js',
					//'node_modules/bootstrap/dist/js/dist/tab.js',
					'node_modules/bootstrap/dist/js/dist/tooltip.js',
					//'node_modules/bootstrap/dist/js/dist/popover.js',
					/* Ractive */
					'node_modules/ractive/ractive.js'
				],
				dest: 'dist/js/app_min.js'
			}
		}

	});


	grunt.registerTask('default', ['css','js']);
	grunt.registerTask('css', ['sass', 'cssmin']);
	grunt.registerTask('js', ['uglify']);

	grunt.loadNpmTasks('grunt-contrib-sass');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-ractive');

};
