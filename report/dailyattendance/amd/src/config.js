define([],function(){
  require.config({
    baseUrl: ".",
    name: "app",
    paths: {
      xlsx: "js/xlsx.full.min",
      filesaver: "js/FileSaver.min",
      blobutil: "js/blob-util.min"
    },

    shim: {
      'xlsx': {exports: 'xlsx'},
      'filesaver': {exports: 'filesaver'},
      'blobutil': {exports: 'blobutil'},
  }

  })
});