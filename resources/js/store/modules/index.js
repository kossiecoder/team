const files = require.context('.', true, /\.js$/);
const modules = {};

files.keys().forEach(fileName => {
    if (fileName ==='./index.js') return;
    if (fileName.indexOf('index.js') !== -1) {
        const split = fileName.split('/');
        const moduleName = split[1];
        modules[moduleName] = files(fileName).default
    }
});
export default modules
