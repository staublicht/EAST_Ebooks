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
			ractive: {
				files: ['src/ractive_components/*.html'],
				tasks: ['clean','ractive','uglify']
			},
			js: {
				files: ['build/*.js','src/*.js'],
				tasks: ['uglify']
			},
			livereload: {
				files: 'dist/**/*',
				options: {
					livereload: true
				}
			}
		},

		sass: {
			build: {
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
			build: {
				src: ['bootstrap.min.js','build/*.js', 'src/main.js'],
				dest: 'dist/js/app.js'
			}
		},

		ractive: {
			options: {
			},
			build: {
				files: {
					'build/' : 'src/ractive_components/*.html'
				}
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
					'node_modules/tether/dist/js/tether.js',
					/* Bootstrap scripts */
					'node_modules/bootstrap/js/dist/util.js',
					'node_modules/bootstrap/js/dist/alert.js',
					'node_modules/bootstrap/js/dist/button.js',
					//'node_modules/bootstrap/js/dist/carousel.js',
					//'node_modules/bootstrap/js/dist/collapse.js',
					'node_modules/bootstrap/js/dist/dropdown.js',
					'node_modules/bootstrapjs/dist/modal.js',
					//'node_modules/bootstrap/js/dist/scrollspy.js',
					//'node_modules/bootstrap/js/dist/tab.js',
					'node_modules/bootstrap/js/dist/tooltip.js',
					//'node_modules/bootstrap/js/dist/popover.js',
					/* Ractive */
					'build/**/*.js',
					'node_modules/ractive/ractive.js',
					'src/main.js'
				],
				dest: 'dist/js/app_min.js'
			}
		},

		clean: {
			build_folder: ['build/']
		}

	});


	grunt.registerTask('default', ['css','js']);
	grunt.registerTask('css', ['sass', 'cssmin']);
	grunt.registerTask('js', ['clean', 'ractive', 'uglify']);

	grunt.loadNpmTasks('grunt-contrib-sass');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-ractive');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-clean');

};