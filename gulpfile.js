'use strict';

// Load plugins
const autoprefixer = require('autoprefixer');
const browsersync = require('browser-sync').create();
const cp = require('child_process');
const cssnano = require('cssnano');
const del = require('del');
const gulp = require('gulp');
const imagemin = require('gulp-imagemin');
const newer = require('gulp-newer');
const plumber = require('gulp-plumber');
const postcss = require('gulp-postcss');
const rename = require('gulp-rename');
const sass = require('gulp-sass');
const uglify = require('gulp-uglify');
const concat = require('gulp-concat');
const terser = require('gulp-terser');
const combineMediaQuery = require('postcss-combine-media-query');

// BrowserSync
function browserSync(done)
{
  browsersync.init({
    open: false,
    //proxy: 'http://pluginsetup.chandresh.php/', // replace it with yours
    port: 3000,
    server: {
      baseDir: './'
    }
  });
  done();
}
// html
function html()
{
  return gulp
  .src([
    './*.html',
  ])
  .pipe(browsersync.stream());
}
// clean
function clean()
{
  return del(['./assets/dist/']);
}

// Images Frontend
function images()
{
  return gulp
  .src('assets/src/front-end/image/**/*')
  .pipe(newer('public/image'))
  .pipe(
    imagemin([
    imagemin.gifsicle({ interlaced: true }),
    imagemin.jpegtran({ progressive: true }),
    imagemin.optipng({ optimizationLevel: 5 }),
    imagemin.svgo({
      plugins: [
      {
        removeViewBox: false,
        collapseGroups: true
      }
      ]
    })
    ])
  )
  .pipe(gulp.dest('public/image'));
}

// Images Dashboard
function imagesBackend()
{
  return gulp
  .src('assets/src/back-end/image/**/*')
  .pipe(newer('admin/images'))
  .pipe(
    imagemin([
    imagemin.gifsicle({ interlaced: true }),
    imagemin.jpegtran({ progressive: true }),
    imagemin.optipng({ optimizationLevel: 5 }),
    imagemin.svgo({
      plugins: [
      {
        removeViewBox: false,
        collapseGroups: true
      }
      ]
    })
    ])
  )
  .pipe(gulp.dest('admin/image'));
}

// CSS
function css()
{
  return gulp
  .src([
    './assets/src/front-end/scss/main.scss',
  ])
  .pipe(plumber())
  .pipe(concat('mwb-public.css'))
  .pipe(sass({ outputStyle: "expanded" }))
  .pipe(gulp.dest("public/css"))
  .pipe(postcss([autoprefixer(), combineMediaQuery()]))
  .pipe(gulp.dest("public/css"))
  .pipe(rename({ suffix: ".min" }))
  .pipe(postcss([cssnano()]))
  .pipe(gulp.dest("public/css"))
  .pipe(browsersync.stream());
}

// CSS Dashboard
function cssBackend()
{
  return gulp
  .src([
    'assets/src/back-end/scss/main.scss',
  ])
  .pipe(plumber())
  .pipe(concat('mwb-admin.css'))
  .pipe(sass({ outputStyle: "expanded" }))
  .pipe(gulp.dest("admin/css"))
  .pipe(postcss([autoprefixer(), combineMediaQuery()]))
  .pipe(gulp.dest("admin/css"))
  .pipe(rename({ suffix: ".min" }))
  .pipe(postcss([cssnano()]))
  .pipe(gulp.dest("admin/css"))
  .pipe(browsersync.stream());
}

// Scripts
function scripts()
{
  return (
  gulp
    .src([
    'assets/src/front-end/js/**/*',
    ])
    .pipe(plumber())
    .pipe(concat('mwb-public.js'))
    .pipe(gulp.dest('public/js'))
    .pipe(terser())
    .pipe(rename({ suffix: '.min' }))
    .pipe(gulp.dest('public/js'))
    .pipe(browsersync.stream())
  );
}

// Scripts Backend
function scriptsBackend()
{
  return (
  gulp
    .src([
    'assets/src/back-end/js/**/*',
    ])
    .pipe(plumber())
    .pipe(concat('mwb-admin.js'))
    .pipe(gulp.dest('admin/js'))
    .pipe(terser())
    .pipe(rename({ suffix: '.min' }))
    .pipe(gulp.dest('admin/js'))
    .pipe(browsersync.stream())
  );
}

// Fonts
function fonts()
{
  return (
  gulp
    .src('assets/src/front-end/fonts/**/*')
    .pipe(plumber())
    .pipe(gulp.dest('public/fonts'))
    .pipe(browsersync.stream())
  );
}

// Fonts Backend
function fontsBackend()
{
  return (
  gulp
    .src('assets/src/back-end/fonts/**/*')
    .pipe(plumber())
    .pipe(gulp.dest('admin/fonts'))
    .pipe(browsersync.stream())
  );
}

// watch changes
function watchFiles()
{
  gulp.watch('./assets/src/front-end/scss/**/*', css);
  gulp.watch('assets/src/front-end/js/**/*', scripts);
  gulp.watch('assets/src/front-end/image/**/*', images);
  gulp.watch('assets/src/front-end/fonts/**/*', fonts);
  gulp.watch('./*.html', html);
  gulp.watch('assets/src/back-end/image/**/*', imagesBackend);
  gulp.watch('assets/src/back-end/scss/**/*', cssBackend);
  gulp.watch('assets/src/back-end/js/**/*', scriptsBackend);
  gulp.watch('assets/src/front-end/fonts/**/*', fontsBackend);
}

const start = gulp.series(clean, images, fonts, css, scripts, html, imagesBackend, cssBackend, scriptsBackend, fontsBackend);
const watch = gulp.parallel(watchFiles, browserSync);

// export tasks
exports.images = images;
exports.css = css;
exports.scripts = scripts;
exports.clean = clean;
exports.imagesBackend = imagesBackend;
exports.cssBackend = cssBackend;
exports.scriptsBackend = scriptsBackend;
exports.fontsBackend = fontsBackend;
exports.watch = watch;
exports.default = gulp.series(start, watch);
