const gulp = require('gulp');
const composer = require('gulp-composer');
const rename = require('gulp-rename');
const merge = require('merge-stream');
const phpcbf = require('gulp-phpcbf');
const beautify = require('gulp-beautify');

gulp.task('php-dependencies', function() {
    return composer('install', {
        "no-interaction": true
    });
});

gulp.task('js-beautify', function() {
    return gulp.src([
            '**/*.js',
            '*.js',
            '**/*.json',
            '*.json'
        ], {
            base: './'
        })
        .pipe(beautify.js({
            indent_size: 4
        }))
        .pipe(gulp.dest('./'));
});

gulp.task('css-beautify', function() {
    return gulp.src([
            '**/*.css',
            '*.css'
        ], {
            base: './'
        })
        .pipe(beautify.css({
            indent_size: 4
        }))
        .pipe(gulp.dest('./'));
});

gulp.task('php-beautify', function() {
    return gulp.src([
            '**/*.php',
            '!./vendor/**/*.php',
            '*.php'
        ], {
            base: './'
        })
        .pipe(phpcbf({
            bin: './vendor/bin/phpcbf',
            standard: 'PSR12',
            warningSeverity: 0
        }))
        .pipe(gulp.dest('./'));
});

gulp.task('js-dependencies', function() {
    var dependencies = {
        src: [
            './node_modules/@popperjs/core/dist/umd/popper.js',
            './node_modules/bootstrap/dist/js/bootstrap.js'
        ],
        name: [
            'popper.js',
            'bootstrap.js'
        ]
    };

    var mergeStreams = merge();
    for (var i in dependencies.src) {
        mergeStreams.add(
            gulp.src(dependencies.src[i])
            .pipe(beautify.js({
                indent_size: 4
            }))
            .pipe(rename(dependencies.name[i]))
            .pipe(gulp.dest('./public/assets/js'))
        );
    }

    return mergeStreams;
});

gulp.task('css-dependencies', function() {
    var dependencies = {
        src: [
            './node_modules/bootstrap/dist/css/bootstrap.css'
        ],
        name: [
            'bootstrap.css'
        ]
    };

    var mergeStreams = merge();
    for (var i in dependencies.src) {
        mergeStreams.add(
            gulp.src(dependencies.src[i])
            .pipe(beautify.css({
                indent_size: 4
            }))
            .pipe(rename(dependencies.name[i]))
            .pipe(gulp.dest('./public/assets/css'))
        );
    }

    return mergeStreams;
});

gulp.task('webfont-dependencies', function() {
    var dependencies = {
        src: [
            './node_modules/bootstrap-icons/font/**/*'
        ]
    };

    var mergeStreams = merge();
    for (var i in dependencies.src) {
        mergeStreams.add(
            gulp.src(dependencies.src[i])
            .pipe(gulp.dest('./public/assets/fonts/bootstrap-icons'))
        );
    }

    return mergeStreams;
});

gulp.task('default', gulp.series('php-dependencies', 'js-beautify', 'css-beautify', 'php-beautify', 'js-dependencies', 'css-dependencies', 'webfont-dependencies'));