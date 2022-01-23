const gulp = require('gulp');
const { series, src, dest, watch } = require('gulp');
// const browserSync = require('browser-sync').create();
const less = require('gulp-less');
//const postcss = require('gulp-postcss');
const rename = require("gulp-rename");
const svgstore = require("gulp-svgstore");
const webp = require("gulp-webp");
const autoprefixer = require('autoprefixer');
const cssmin = require('gulp-cssmin');
// const cssnano = require('cssnano');
// const fileinclude = require('gulp-file-include');
const babel = require("gulp-babel");
const plumber = require('gulp-plumber');
const webpack = require('webpack-stream');
const path = require('path');
const gulpif = require('gulp-if');
const del = require('del');
const cssnext = require('cssnext');

const plugins = [
  autoprefixer,
  cssnext,
];

let isDevelopment = false;

const webpackConfig = {
  entry: {
  //  search : path.resolve(__dirname, 'themes/ladybrands/assets/src/js/search.js'),
    default : path.resolve(__dirname, 'themes/ladybrands/assets/src/js/default.js'),
    //productPage : path.resolve(__dirname, 'themes/ladybrands/assets/src/js/productPage.js'),
    // newsPage : path.resolve(__dirname, 'themes/ladybrands/assets/src/js/newsPage.js'),
    //indexPage : path.resolve(__dirname, 'themes/ladybrands/assets/src/js/indexPage.js'),
    catalog : path.resolve(__dirname, 'themes/ladybrands/assets/src/js/catalog.js'),
    cart : path.resolve(__dirname, 'themes/ladybrands/assets/src/js/cart.js'),
    cartPage : path.resolve(__dirname, 'themes/ladybrands/assets/src/js/cartPage.js'),
    account : path.resolve(__dirname, 'themes/ladybrands/assets/src/js/account.js'),
    index : path.resolve(__dirname, 'themes/ladybrands/assets/src/js/index.js'),
    // portfolioPage : path.resolve(__dirname, 'themes/ladybrands/assets/src/js/portfolioPage.js'),


  },
  output: {
    path: path.resolve(__dirname, 'themes/ladybrands/assets/javascript'),
    filename: '[name].js',
  },
  resolve: {
    modules: [
      path.join(__dirname, "themes/ladybrands/assets/src/js/module"),
      "node_modules"
    ]
  },
  mode: isDevelopment ? 'development' : 'production',
  devtool: isDevelopment ?  'eval-source-map' : 'none',

  module: {
    rules: [{
      test: /\.m?js$/,
      exclude: /(node_modules|bower_components)/,
      use: {
        loader: 'babel-loader',
        options: {
          presets: ['@babel/preset-env']
        }
      }
    }]
  }
};

function cssRender(name) {
  src('themes/ladybrands/assets/src/less/' + name + '.less')
    .pipe(plumber())
    .pipe(less())
    .pipe(gulpif(!isDevelopment, cssmin()))
    .pipe(gulpif(!isDevelopment, postcss(plugins)))
    .pipe(rename(name + '.min.css'))
    .pipe(dest('themes/ladybrands/assets/css'));
}

function inlineRender() {
  src('themes/ladybrands/assets/src/less/inline.less')
    .pipe(plumber())
    .pipe(less())
    .pipe(postcss(plugins))
    .pipe(rename("inline-style.htm"))
    .pipe(dest('themes/ladybrands/partials/layout-components'));
}

function bundle(done) {
  src('themes/ladybrands/assets/src/js/default.js')
    .pipe(plumber())
    .pipe(webpack(webpackConfig))
    .pipe(dest('themes/ladybrands/assets/javascript'));
    done();
}

function lessToCss(done) {
  cssRender('styles');
  inlineRender();
  cssRender('indexPage');
  cssRender('routingCatalog');
  cssRender('routingArticles');
  // cssRender('innerPage');

  done();
}

function inline(done) {
  inlineRender();
  done();
}

function styles(done) {
  cssRender('styles');
  done();
}

function indexPage(done) {
  cssRender('indexPage');
  done();
}

function routingCatalog(done) {
  cssRender('routingCatalog');
  done();
}

function routingArticles(done) {
  cssRender('routingArticles');
  done();
}

function innerPage(done) {
  cssRender('innerPage');
  done();
}


function watching(done) {
  watch("themes/ladybrands/assets/src/less/blocks/**/*.less", series(lessToCss));
  watch("themes/ladybrands/assets/src/js/**/*.js", series(bundle));

  watch("themes/ladybrands/assets/src/less/inline.less", series(inline));
  watch("themes/ladybrands/assets/src/less/styles.less", series(styles));
  watch("themes/ladybrands/assets/src/less/indexPage.less", series(indexPage));
  watch("themes/ladybrands/assets/src/less/routingCatalog.less", series(routingCatalog));
  watch("themes/ladybrands/assets/src/less/routingArticles.less", series(routingArticles));

  done();
}

function update(done) {
  src('themes/ladybrands/assets/src/js/default.js')
    .pipe(plumber())
    .pipe(webpack(webpackConfig))
    .pipe(dest('themes/ladybrands/assets/javascript'));

    inlineRender();
    cssRender("styles");
    cssRender("indexPage");
    done();
}


exports.default = series(watching);
exports.update = series(update);

// png or jpg to webp
gulp.task("webp", function () {
  return src("themes/ladybrands/assets/images/*.{png,jpg,jpeg}")
  .pipe(webp({quality: 85}))
  .pipe(dest("themes/ladybrands/assets/images/"));
});

gulp.task("deleteWebp", function () {
  return del(['themes/ladybrands/assets/images/*.webp']);
});
