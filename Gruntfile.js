/*global module */

module.exports = function(grunt) {
  'use strict';
  // Project configuration.
  grunt.registerTask('watch', [ 'watch' ]);
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    // Lint JavaScript
    jshint: {
      all: ['js/spw-admin.js'],
       options:{
        "forin": true,
        "noarg": true,
        "noempty": true,
        "eqeqeq": true,
        "bitwise": true,
        "undef": true,
        "unused": false,
        "curly": true,
        "browser": true,
        "strict":  false
      }
    },
    uglify: {
        all: {
            options: {
                preserveComments: "some"
            },
            files: {
                "js/spw-admin.min.js": [
                    "js/*.js",
                    "!js/*.min.js",
                ],
            }
        }
    },
    cssmin: {
       minify: {
       expand: true,
       cwd: 'css/',
       src: [
        '*.css',
        '!*.min.css'
       ],
       dest: 'css/',
       ext: '.min.css'
      }
    },
    sass: {
      dist: {
        files: [{
          expand: true,
          cwd: 'sass',
          src: ['*.scss'],
          dest: './css',
          ext: '.css'
        }]
      }
    },
    watch: {
      js: {
        files: ['js/*.js'],
        tasks: ['jshint', 'uglify'],
        options: {
          livereload: true,
        }
      },
      css: {
        files: ['sass/*.scss'],
        tasks: ['sass', 'cssmin'],
        options: {
          livereload: true,
        }
      }
    }


  });

  // Load npm plugins to provide necessary tasks.
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-sass');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-contrib-watch');

  // Default tasks to be run.
  grunt.registerTask('default', [
    'jshint', 'uglify', 'sass', 'cssmin'
  ]);

};