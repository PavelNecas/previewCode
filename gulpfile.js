let gulp = require("gulp");
let sass = require("gulp-sass")(require('sass'));
let sourcemaps = require('gulp-sourcemaps');
let autoprefixer = require("gulp-autoprefixer");
let concat = require("gulp-concat");
let uglify = require('gulp-uglify-es').default;
let csso = require('gulp-csso');
let rename = require('gulp-rename');
const { series } = require('gulp');

var css = {
    src: [
        'public/static/scss/lib/_bootstrap.scss',
        'public/static/scss/src/_variable.scss',
        'public/static/scss/src/_helpers.scss',
        'public/static/scss/src/_typo.scss',
        'public/static/scss/lib/**/*.scss',
        'public/static/scss/src/**/*.scss'
    ],
    dest: 'public/static/css/',
    filename: 'styles.css'
};

var js = {
    src: [
        'public/static/js/lib/**/*.js',
        'public/static/js/src/**/*.js'
    ],
    dest: 'public/static/js/',
    filename: 'scripts.js'
};

function style() {
    return (
        gulp
            .src(css.src)
            .pipe(concat(css.filename))
            .pipe(sass())
            .on("error", sass.logError)
            .pipe(autoprefixer())
            .pipe(gulp.dest(css.dest))
            .pipe(csso())
            .pipe(rename({extname: '.min.css'}))
            .pipe(gulp.dest(css.dest))
    );
}

function styleDev() {
    return (
        gulp
            .src(css.src)
            .pipe(sourcemaps.init())
            .pipe(concat(css.filename))
            .pipe(sass({outputStyle: 'expanded'}))
            .on("error", sass.logError)
            .pipe(autoprefixer({cascade: false}))
            .pipe(sourcemaps.write('.'))
            .pipe(gulp.dest(css.dest))
    );
}

function script() {
    return (
        gulp
            .src(js.src)
            .pipe(concat(js.filename))
            .pipe(gulp.dest(js.dest))
            .pipe(uglify())
            .pipe(rename({extname: '.min.js'}))
            .pipe(gulp.dest(js.dest))
    );
}

function scriptDev() {
    return (
        gulp
            .src(js.src)
            .pipe(sourcemaps.init())
            .pipe(concat(js.filename))
            .pipe(sourcemaps.write('.'))
            .pipe(gulp.dest(js.dest))
    );
}

function watch(){
    gulp.watch(css.src, style);
    gulp.watch(js.src, script);
    exports.css = style;
    exports.js = script;
}

function devel(){
    gulp.watch(css.src, styleDev);
    gulp.watch(js.src, scriptDev);
    exports.css = styleDev;
    exports.js = scriptDev;
}

function build(){

}

exports.dev = devel;
exports.default = watch;
exports.build = series(style, script)
