var gulp = require('gulp');
var browserSync = require('browser-sync');
var uglify = require('gulp-uglify');
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

gulp.task('browser-sync', function() {
    browserSync({
        server: {
            baseDir: './public'
        },
        injectChanges: true,
        open: false
    });
});

gulp.task('template', function() {
    return gulp.src('./src/*.html')
        .pipe(gulp.dest('./public/'));
});

/* zeroClipboard libs */
gulp.task('libs', function() {
    return gulp.src('./src/js/*.swf')
        .pipe(gulp.dest('./public/js/'));
});

gulp.task('fonts', function() {
    return gulp.src('./src/fonts/**/*.*')
        .pipe(gulp.dest('./public/fonts/'));
});

gulp.task('images', function() {
    return gulp.src('./src/images/**/*.*')
        .pipe(gulp.dest('./public/images/'));
});

gulp.task('style', function () {
    return gulp.src('./src/less/style.less')
    .pipe(less({compress: true}))
    .pipe(gulp.dest('./public/css'))
    .pipe(prefixer('last 5 versions', 'ie 8'))
    .pipe(browserSync.reload({stream:true}));
    }
);

/**
 * Browserify poop
 */
var bundler = watchify(browserify('./src/js/app.js', watchify.args));
// add any other browserify options or transforms here
bundler.transform([reactify]);

gulp.task('script', bundle); // so you can run `gulp js` to build the file
bundler.on('update', bundle); // on any dep update, runs the bundler

function bundle() {
  return bundler.bundle()
    // log errors if they happen
    .on('error', gutil.log.bind(gutil, 'Browserify Error'))
    .pipe(source('bundle.js'))
    // optional, remove if you dont want sourcemaps
      .pipe(buffer())
      .pipe(sourcemaps.init({loadMaps: true})) // loads map from browserify file
      .pipe(sourcemaps.write('./')) // writes .map file
    //
    .pipe(gulp.dest('./public/js'))
    .pipe(reload({stream:true}));
}

gulp.task('default', ['script', 'libs', 'images', 'fonts', 'style', 'template', 'browser-sync'], function() {
    gulp.watch('./src/*.html', ['template', browserSync.reload]);
    gulp.watch('./src/less/**/*.less', ['style']);
    gulp.watch('./src/fonts/**/*.*', ['fonts']);
});
