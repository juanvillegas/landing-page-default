var gulp = require('gulp');
var sass = require('gulp-sass');
var autoprefixer = require('gulp-autoprefixer');
var minifyCSS = require('gulp-minify-css');
var concat = require('gulp-concat');
var minifyJS = require('gulp-uglify');

gulp.task('css', function(){
    gulp.src('./css/styles.sass')
        .pipe(sass())
        .pipe(autoprefixer('last 15 version'))
        .pipe(minifyCSS({keepBreaks:true}))
        .pipe(gulp.dest('css'));
});


gulp.task('watch', function(){
    gulp.watch('./css/styles.sass', ['css'])
});

gulp.task('default', ['watch', 'scripts']);



gulp.task('scripts', function() {
    gulp.src('./js/vendor/**/*.js')
        .pipe(concat('vendor.js'))
        .pipe(minifyJS())
        .pipe(gulp.dest('./js/'))
});