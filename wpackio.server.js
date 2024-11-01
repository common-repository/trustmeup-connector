module.exports = {
  host: undefined,
  proxy: "http://playground.wp",
  port: 3000,
  ui: {
    port: 3001,
    files: ["wp-content/plugins/trustmeup/**/*.css"],
    watch: true,
  },
  notify: false,
  open: false,
  ghostMode: {
    clicks: true,
    scroll: false,
    forms: false,
  },
  distPublicPath: undefined,
  jsBabelOverride: (defaults) => ({
    ...defaults,
    plugins: ["react-hot-loader/babel"],
  }),
};
