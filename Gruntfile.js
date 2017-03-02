module.exports = function ( grunt ) {

    'use strict';

    // Project configuration.
    grunt.initConfig({

        pkg: grunt.file.readJSON('package.json'),

        // Bump version numbers.
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
        }

    });

    require('load-grunt-tasks')( grunt );
    grunt.registerTask('pre-tag', ['version']);

    grunt.util.linefeed = '\n';

};
