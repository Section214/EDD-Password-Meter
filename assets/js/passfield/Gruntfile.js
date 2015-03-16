module.exports = function(grunt) {

    grunt.initConfig({
        pkg: grunt.file.readJSON("package.json"),
        qunit: {
            all: ["test/**/unit-tests.html"]
        },
        clean: {
            options: {
                force: true
            },
            src: ["../build-v1/css/", "../build-v1/js/", "../build-v1/img/", "../site/passfield"]
        },
        uglify: {
            options: {
                banner: "/*! <%= pkg.name %> v<%= pkg.version %>  | (c) <%= grunt.template.today('yyyy') %> Antelle | https://github.com/antelle/passfield/blob/master/MIT-LICENSE.txt */\n"
            },
            color_picker: {
                files: {
                    "../build-v1/js/passfield.min.js": ["js/*.js"]
                }
            }
        },
        cssmin: {
            compress: {
                files: {
                    "../build-v1/css/passfield.min.css": ["css/*.css"]
                }
            }
        },
        copy: {
            color_picker: {
                files: [
                    { expand: true, src: ["img/*.jpg", "img/*.png"], dest: "../build-v1/" }
                ]
            },
            site: {
                files: [
                    { expand: true, flatten: true, src: ["../build-v1/img/*.jpg", "../build-v1/img/*.png"], dest: "../site/passfield/img/" },
                    { expand: true, flatten: true, src: "../build-v1/js/*.js", dest: "../site/passfield/js/" },
                    { expand: true, flatten: true, src: "../build-v1/css/*.css", dest: "../site/passfield/css/" }
                ]
            }
        }
    });

    grunt.loadNpmTasks("grunt-contrib-qunit");
    grunt.loadNpmTasks("grunt-contrib-clean");
    grunt.loadNpmTasks("grunt-contrib-uglify");
    grunt.loadNpmTasks("grunt-contrib-uglify");
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks("grunt-contrib-copy");

    grunt.registerTask("default", ["qunit", "clean", "uglify", "cssmin", "copy"]);

};
