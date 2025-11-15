module.exports = function(grunt) {
    grunt.initConfig({
        clean: {
            build: ['build']
        },
        copy: {
            main: {
                expand: true,
                src: [
                    '**',
                    '!node_modules/**',
                    '!assets/src/**',
                    '!build/**',
                    '!.git/**',
                    '!.gitignore',
                    '!Gruntfile.js',
                    '!package.json',
                    '!package-lock.json',
                    '!webpack.config.js',
                    '!.babelrc',
                    '!**/*.md',
                    'README.md'
                ],
                dest: 'build/ninja-test-email/'
            },
            wordpress: {
                expand: true,
                src: [
                    '**',
                    '!node_modules/**',
                    '!assets/src/**',
                    '!build/**',
                    '!.git/**',
                    '!.gitignore',
                    '!Gruntfile.js',
                    '!package.json',
                    '!package-lock.json',
                    '!webpack.config.js',
                    '!.babelrc',
                    '!**/*.md',
                    'README.md'
                ],
                dest: 'E:/local-sites/app/public/wp-content/plugins/ninja-test-email'
            }
        },
        compress: {
            main: {
                options: {
                    archive: 'build/ninja-test-email.zip'
                },
                files: [{
                    expand: true,
                    cwd: 'build/',
                    src: ['ninja-test-email/**'],
                    dest: '/'
                }]
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-compress');

    grunt.registerTask('package', ['clean:build', 'copy:main', 'copy:wordpress', 'compress:main']);

    grunt.registerTask('reload_wp', 'Touch a file to trigger reload', function() {
        const fs = require('fs');
        fs.utimesSync('E:/local-sites/app/public/wp-content/plugins/ninja-test-email/ninja-test-email.php', new Date(), new Date());
        grunt.log.writeln('Plugin file touched to trigger reload');
    });
};
