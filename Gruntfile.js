/*global module */

module.exports = function(grunt) {
  'use strict';
  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    // Lint JavaScript
    jshint: {
      all: ['javascripts/spw-admin.js'],
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
                "javascripts/spw-admin.min.js": [
                    "javascripts/*.js",
                    "!javascripts/*.min.js",
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
    }

  });

  // Load npm plugins to provide necessary tasks.
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-sass');
  grunt.loadNpmTasks('grunt-contrib-cssmin');

  // Default tasks to be run.
  grunt.registerTask('default', [
    'jshint', 'uglify', 'sass', 'cssmin'
  ]);

};