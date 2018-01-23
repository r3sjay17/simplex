var gulp = require('gulp');
var sass = require('gulp-sass');
var cssnano = require('gulp-cssnano');
var rename = require('gulp-rename');
var sourcemaps = require('gulp-sourcemaps');

gulp.task('sass', function(){
    var sass_src = ['./assets/scss/**/*.scss'];
    return gulp.src(sass_src)
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
	.pipe(gulp.dest('./assets/css'))
        .pipe(cssnano())
        .pipe(sourcemaps.write())
        .pipe(rename(function(p){ p.extname = '.min.css' }))
        .pipe(gulp.dest('./assets/css'));
});

gulp.task('watch', function(){
    gulp.watch('./assets/scss/**/*.scss', ['sass']);
});
