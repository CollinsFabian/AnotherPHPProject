// tools/builder/watch.js
const chokidar = require('chokidar');
const { exec } = require('child_process');
const { broadcast } = require('./reload-server');

console.log('Watching files...');

chokidar.watch('../../resources').on('change', (path) => {
    console.log(`Changed: ${path}`);

    exec('node build.js', { cwd: __dirname }, () => {
        broadcast();
    });
});
