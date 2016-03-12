module.exports = function (grunt) {


    // Project configuration.
    grunt.initConfig({
        pkg     : grunt.file.readJSON( 'package.json' ),
        shell: {
            composer: {
                command: 'composer update --no-dev --optimize-autoloader --prefer-dist'
            },
            bower: {
                command: 'bower update'
            },
            activate : {
                command: 'composer update --no-dev; bower update; bower-installer'
            }
        },
        clean: {
            post_build: [
                'build/'
            ],
            pre_compress: [
                'build/ingot/vendor/ingot/mabandit/test',
                'build/ingot/vendor/ingot/mabandit/vendor/phpunit',
                'build/ingot/vendor/ingot/mabandit/vendor/symfony',
                'build/ingot/vendor/jaybizzle/crawler-detect/tests',
                'build/ingot/vendor/blainesch/prettyarray/examples',
                'build/ingot/vendor/bin',
                'build/releases',
                'build/ingot/build'
            ]
        },
        run: {
            tool: {
                cmd: './composer'
            }
        },
        copy: {
            build: {
                options : {
                    mode :true
                },
                src: [
                    '**',
                    '!node_modules/**',
                    '!releases',
                    '!releases/**',
                    '!.git/**',
                    '!Gruntfile.js',
                    '!package.json',
                    '!.gitignore',
                    '!.gitmodules',
                    '!.gitattributes',
                    '!composer.lock',
                    '!naming-conventions.txt',
                    '!how-to-grunt.md',
                    '!.travis.yml',
                    '!.scrutinizer.yml',
                    '!phpunit.xml',
                    '!tests/**',
                    '!bower_components/**',
                    '!bin/**',
                    '!vendor/ingot/mabandit/vendor/bin/**'

                ],
                dest: 'build/<%= pkg.name %>/'
            }
        },
        compress: {
            main: {
                options: {
                    mode: 'zip',
                    archive: 'releases/<%= pkg.name %>-<%= pkg.version %>.zip'
                },
                expand: true,
                cwd: 'build/',
                src: [
                    '**/*',
                    '!build/*'
                ]
            }
        },
        gitadd: {
            add_zip: {
                options: {
                    force: true
                },
                files: {
                    src: [ 'releases/<%= pkg.name %>-<%= pkg.version %>.zip' ]
                }
            }
        },
        gittag: {
            addtag: {
                options: {
                    tag: '<%= pkg.version %>',
                    message: 'Version <%= pkg.version %>'
                }
            }
        },
        gitcommit: {
            commit: {
                options: {
                    message: 'Version <%= pkg.version %>',
                    noVerify: true,
                    noStatus: false,
                    allowEmpty: true
                },
                files: {
                    src: [ 'package.json', 'ingot.php', 'releases/<%= pkg.name %>-<%= pkg.version %>.zip' ]
                }
            }
        },
        gitpush: {
            push: {
                options: {
                    tags: true,
                    remote: 'origin',
                    branch: 'master'
                }
            }
        },
        replace: {
            core_file: {
                src: [ 'ingot.php' ],
                overwrite: true,
                replacements: [{
                    from: /Version:\s*(.*)/,
                    to: "Version: <%= pkg.version %>"
                }, {
                    from: /define\(\s*'INGOT_VER',\s*'(.*)'\s*\);/,
                    to: "define( 'INGOT_VER', '<%= pkg.version %>' );"
                }]
            }
        },
        uglify: {
            frontend: {
                files: {
                    'vendor/ingot/ingot-core/assets/front-end/js/ingot-click-test.min.js': [ 'vendor/ingot/ingot-core/assets/front-end/js/ingot-click-test.js' ],
                    'vendor/ingot/ingot-core/assets/admin/js/ingot-post-editor.min.js' : ['vendor/ingot/ingot-core/assets/admin/js/ingot-post-editor.js' ]
                }
            },
            admin:{
                files: {
                    'vendor/ingot/ingot-core/assets/admin/js/admin-app.min.js': [ 'vendor/ingot/ingot-core/assets/admin/js/admin-app.js' ]
                }
            }
        },
        watch: {
            files: [
                'vendor/ingot/ingot-core/assets/admin/js/admin-app.js',
                'vendor/ingot/ingot-core/assets/front-end/js/ingot-click-test.js'
            ],
            tasks: ['default']
        },
        addtextdomain: {
            options: {
                textdomain: 'ignot',
            },
            target: {
                files: {
                    src: [ '*.php', '**/*.php', '!node_modules/**', '!tests/**', '!bin/**' ]
                }
            }
        },
        makepot: {
            target: {
                options: {
                    domainPath: '/languages',
                    mainFile: 'ingot.php',
                    potFilename: 'ingot.pot',
                    potHeaders: {
                        poedit: true,
                        'x-poedit-keywordslist': true
                    },
                    type: 'wp-plugin',
                    updateTimestamp: true
                }
            }
        },

    });

    //load modules
    grunt.loadNpmTasks( 'grunt-contrib-compress' );
    grunt.loadNpmTasks( 'grunt-contrib-clean' );
    grunt.loadNpmTasks( 'grunt-contrib-copy' );
    grunt.loadNpmTasks( 'grunt-git' );
    grunt.loadNpmTasks( 'grunt-text-replace' );
    grunt.loadNpmTasks( 'grunt-shell');
    grunt.loadNpmTasks( 'grunt-contrib-uglify');
    grunt.loadNpmTasks( 'grunt-contrib-watch');
    grunt.loadNpmTasks( 'grunt-wp-i18n' );
    grunt.registerTask( 'i18n', ['addtextdomain', 'makepot'] );



    //register default task
    grunt.registerTask( 'default', [ 'uglify' ]);

    //release tasks
    grunt.registerTask( 'version_number', [ 'replace:core_file' ] );
    grunt.registerTask( 'pre_vcs', [ 'shell:activate', 'version_number', 'copy', 'clean:pre_compress', 'compress' ] );
    grunt.registerTask( 'do_git', [ 'gitadd', 'gitcommit', 'gittag', 'gitpush' ] );
    grunt.registerTask( 'just_build', [  'shell:composer', 'copy', 'clean:pre_compress', 'compress' ] );
    grunt.registerTask( 'install', [ 'shell:activate' ] );

    grunt.registerTask( 'release', [ 'pre_vcs', 'do_git', 'clean:post_build' ] );


};
