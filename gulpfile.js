var gulp = require('gulp');
var sass = require('gulp-sass');
var autoprefixer = require('gulp-autoprefixer');
var concat = require('gulp-concat');
var cleanCss = require('gulp-clean-css');
var watch = require('gulp-watch');
var rev = require('gulp-rev');
var revReplace = require('gulp-rev-replace');
var browserSync = require('browser-sync').create();
var reload = browserSync.reload;
var del = require('del');


gulp.task('default',['replace'], function() {
  // place code for your default task here
});

gulp.task('clean', function() {
  return del([
    'assets/css/*.css',
    'assets/js/*.js'
  ])
});

gulp.task('sass', function() {
  return gulp.src('assets/css/src/acles-form.scss')
              .pipe(sass().on('error', sass.logError) )
              .pipe(autoprefixer())
              .pipe(rev())
              .pipe(gulp.dest('assets/css'))
              .pipe(rev.manifest({
                base: 'assets/css',
                merge: true
              }))
              .pipe(gulp.dest('assets/css'));
});

gulp.task('concatjs', function() {
  return gulp.src([
            'assets/js/vendor/jquery.cookie.js',
            'assets/js/vendor/jquery.Rut.min.js',
            'assets/js/vendor/jquery.steps.min.js',
            'assets/js/vendor/jquery.validate.min.js',
            'assets/js/src/utils-acles.js',
            'assets/js/src/ajax-acles.js',
            'assets/js/src/document-ready-acles.js'
          ])
          .pipe(concat('main-acles-build.js'))
          .pipe(rev())
          .pipe(gulp.dest('assets/js'))
          .pipe(rev.manifest({
             base: 'assets/js',
             merge: true
          }))
          .pipe(gulp.dest('assets/js'));
});

gulp.task('replace', ['clean', 'concatjs', 'sass'], function() {
  var manifest = gulp.src("./rev-manifest.json");
  return gulp.src('scripts.php')
              .pipe(revReplace({
                  manifest:manifest,
                  replaceInExtensions:['.php']
                }))
              .pipe(gulp.dest(''))
});

gulp.task('watch', function() {
  gulp.watch([
    'assets/css/src/*.scss',
    'assets/js/src/*.js'
  ],
  [
    'clean',
    'replace'
  ])
});