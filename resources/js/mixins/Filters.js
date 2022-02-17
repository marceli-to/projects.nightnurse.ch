Vue.filter('truncate', function (text, length, suffix) {
  let t = text.replace(/(<([^>]+)>)/ig,"");
  if (t.length > length) {
    return t.substring(0, length) + suffix;
  }
  else {
    return t;
  }
});

Vue.filter('filesize', function(size){
  const i = Math.floor( Math.log(size) / Math.log(1024) );
  return ( size / Math.pow(1024, i) ).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i];
});