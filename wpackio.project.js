const pkg = require("./package.json");

module.exports = {
  // Project Identity
  appName: "TrustMeUp", // Unique name of your project
  type: "plugin", // Plugin or theme
  slug: "trustmeup", // Plugin or Theme slug, basically the directory name under `wp-content/<themes|plugins>`
  // Used to generate banners on top of compiled stuff
  bannerConfig: {
    name: "TrustMeUp",
    author: "",
    license: "UNLICENSED",
    link: "UNLICENSED",
    version: pkg.version,
    copyrightText:
      "This software is released under the UNLICENSED License\nhttps://opensource.org/licenses/UNLICENSED",
    credit: true,
  },
  // Files we need to compile, and where to put
  files: [
    {
      name: "admin_js",
      entry: {
        admin: ["./assets/js/admin.js"],
      },
    },
    {
      name: "admin_css",
      entry: {
        admin: ["./assets/css/admin.scss"],
      },
    },
  ],
  // Output path relative to the context directory
  // We need relative path here, else, we can not map to publicPath
  outputPath: "dist",
  // Project specific config
  // Needs react(jsx)?
  hasReact: true,
  // Disable react refresh
  disableReactRefresh: false,
  // Needs sass?
  hasSass: true,
  // Needs less?
  hasLess: false,
  // Needs flowtype?
  hasFlow: false,
  // Externals
  // <https://webpack.js.org/configuration/externals/>
  externals: {
    jquery: "jQuery",
  },
  // Webpack Aliases
  // <https://webpack.js.org/configuration/resolve/#resolve-alias>
  alias: undefined,
  // Show overlay on development
  errorOverlay: true,
  // Auto optimization by webpack
  // Split all common chunks with default config
  // <https://webpack.js.org/plugins/split-chunks-plugin/#optimization-splitchunks>
  // Won't hurt because we use PHP to automate loading
  optimizeSplitChunks: true,
  // Usually PHP and other files to watch and reload when changed
  watch: "./inc|includes/**/*.php",
  // Files that you want to copy to your ultimate theme/plugin package
  // Supports glob matching from minimatch
  // @link <https://github.com/isaacs/minimatch#usage>
  packageFiles: [
    "includes/**",
    "vendor/**",
    "dist/**",
    "assets/images/**",
    "*.php",
    "*.md",
    "readme.txt",
    "languages/**",
    "LICENSE",
    "*.css",
    "*.jpg",
  ],
  // Path to package directory, relative to the root
  packageDirPath: "package",
};
