"use strict";

var gulp = require('gulp');
var browserSync = require('browser-sync');
var del = require('del');
var uglify = require('gulp-uglify');
var jshint = require('gulp-jshint');
var source = require('vinyl-source-stream');
var buffer = require('vinyl-buffer');
var gutil = require('gulp-util');
var watchify = require('watchify');
var reactify = require('reactify');
var reload = browserSync.reload;
var sourcemaps = require('gulp-sourcemaps');
var browserify = require('browserify');
var less = require('gulp-less');
var prefixer = require('gulp-autoprefixer');

var src = './src',
    output =  './public',
    sync = true;

gulp.task('clean', function (cb) {
    del([
        output + '/**'
    ], cb);
});

gulp.task('browser-sync', function() {
    browserSync({
        server: {
            baseDir: output + ''
        },
        injectChanges: true,
        open: false
    });
});

gulp.task('template', function() {
    return gulp.src(src + '/*.html')
        .pipe(gulp.dest(output + '/'));
});

/* zeroClipboard libs */
gulp.task('libs', function() {
    return gulp.src(src + '/js/*.swf')
        .pipe(gulp.dest(output + '/js/'));
});

gulp.task('fonts', function() {
    return gulp.src(src + '/fonts/**/*.*')
        .pipe(gulp.dest(output + '/fonts/'));
});

gulp.task('images', function() {
    return gulp.src(src + '/images/**/*.*')
        .pipe(gulp.dest(output + '/images/'));
});

gulp.task('style', function () {
    var result = gulp.src(src + '/less/style.less')
    .pipe(less({compress: true}))
    .pipe(gulp.dest(output + '/css'))
    .pipe(prefixer('last 5 versions', 'ie 8'));
    if(sync) {
        result.pipe(browserSync.reload({stream:true}));
    }

    return result;
});


gulp.task('lint', function() {
    return gulp.src('/js/app.js')
        .pipe(jshint(src + '/js/.jshintrc'))
        .pipe(jshint.reporter('default'));
});

/**
 * Browserify poop
 */
var bundle = function() {

    if(!sync) {
        var bundler = browserify(src + '/js/app.js');
        bundler.transform([reactify]);
        return bundler.bundle()
            .on('error', gutil.log.bind(gutil, 'Browserify Error'))
            .pipe(source('bundle.js'))
            .pipe(buffer())
            .pipe(uglify())
            .pipe(gulp.dest(output + '/js'));
    } else {
        var bundler = watchify(browserify(src + '/js/app.js', watchify.args));
        bundler.transform([reactify]);
        bundler.on('update', bundle); // on any dep update, runs the bundler

        return bundler.bundle()
            .on('error', gutil.log.bind(gutil, 'Browserify Error'))
            .pipe(source('bundle.js'))
            .pipe(buffer())
            .pipe(sourcemaps.init({loadMaps: true}))
            .pipe(sourcemaps.write('./'))
            .pipe(gulp.dest(output + '/js'))
            .pipe(reload({stream: true}));
    }
}

gulp.task('script', bundle); // so you can run `gulp js` to build the file

gulp.task('default', ['clean'], function() {
    sync = true;
    gulp.start(['lint', 'script', 'libs', 'images', 'fonts', 'style', 'template', 'browser-sync'], function () {
        gulp.watch(src + '/*.html', ['template', browserSync.reload]);
        gulp.watch(src + '/less/**/*.less', ['style']);
        gulp.watch(src + '/fonts/**/*.*', ['fonts']);
    });
});

gulp.task('build', ['clean'], function() {
    sync = false;
    gulp.start('lint', 'libs', 'images', 'fonts', 'style', 'template', 'script');
});
