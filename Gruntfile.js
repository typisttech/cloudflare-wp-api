module.exports = function (grunt) {

    'use strict';

    // Project configuration
    grunt.initConfig({

        pkg: grunt.file.readJSON('package.json'),

        // Bump version numbers
        version: {
            composer: {
                options: {
                    prefix: '"version"\\:\\s+"'
                },
                src: ['composer.json']
            },
            changelog: {
                options: {
                    prefix: 'future-release='
                },
                src: ['.github_changelog_generator']
            }
        },

        // Install composer dependencies and generate autoloader
        exec: {
            composer_update: {
                command: 'composer update --no-interaction --no-suggest --optimize-autoloader'
            },
            changelog: {
                command: 'github_changelog_generator'
            },
            readme_toc: {
                command: 'doctoc README.md'
            }
        }

    });

    require('load-grunt-tasks')(grunt);
    grunt.registerTask('markdown', ['exec:changelog', 'exec:readme_toc']);
    grunt.registerTask('pretag', ['version', 'exec:composer_update', 'markdown']);

    grunt.util.linefeed = '\n';

};
