// tools/builder/dev.js
const esbuild = require('esbuild');
const chokidar = require('chokidar');
const fs = require('fs');
const path = require('path');
const crypto = require('crypto');
const WebSocket = require('ws');

const ROOT = path.resolve(__dirname, '../../');
const RESOURCES = path.join(ROOT, 'resources');
const PUBLIC = path.join(ROOT, 'public');

const PORT = 3001;
const WS_PORT = 3002;

// --- Live reload WS ---
const wss = new WebSocket.Server({ port: WS_PORT });
function broadcastReload(type, file) {
    wss.clients.forEach(client => {
        if (client.readyState === 1) client.send(JSON.stringify({ type, file }));
    });
}

// --- Hash helper ---
function hash(content) {
    return crypto.createHash('md5').update(content).digest('hex').slice(0, 8);
}

// --- Manifest ---
const manifestPath = path.join(PUBLIC, 'manifest.json');
let manifest = {};
if (fs.existsSync(manifestPath)) {
    manifest = JSON.parse(fs.readFileSync(manifestPath, 'utf-8'));
}

// --- Ensure output dirs ---
['js', 'css'].forEach(dir => {
    const full = path.join(PUBLIC, dir);
    if (!fs.existsSync(full)) fs.mkdirSync(full, { recursive: true });
});

// --- Clean old files ---
function cleanOldFiles(dir, base, ext) {
    fs.readdirSync(dir).forEach(f => {
        if (f.startsWith(base) && f.endsWith(ext)) fs.unlinkSync(path.join(dir, f));
    });
}

// --- Build CSS with HMR ---
async function buildCSS(file) {
    const src = path.join(RESOURCES, 'css', file);
    const outDir = path.join(PUBLIC, 'css');
    let content = fs.readFileSync(src, 'utf-8');
    content = content.replace(/\s+/g, ' ').trim();

    // remove old built CSS
    cleanOldFiles(outDir, file.replace('.css', ''), '.css');

    const h = hash(content);
    const outFile = `${file.replace('.css', '')}.${h}.css`;
    fs.writeFileSync(path.join(outDir, outFile), content);

    manifest[`css/${file}`] = `css/${outFile}`;
    fs.writeFileSync(manifestPath, JSON.stringify(manifest, null, 2));

    // HMR broadcast
    broadcastReload('css', `css/${outFile}`);
    console.log(`[HMR] CSS updated: ${outFile}`);
}

// --- Rebuild all CSS initially ---
async function rebuildAllCSS() {
    const cssFiles = fs.readdirSync(path.join(RESOURCES, 'css')).filter(f => f.endsWith('.css'));
    for (const f of cssFiles) await buildCSS(f);
}

// --- Start JS dev server with proper rebuild hook ---
async function startJSDevServer() {
    const jsFiles = fs.readdirSync(path.join(RESOURCES, 'js')).filter(f => f.endsWith('.js'));
    const jsEntries = jsFiles.map(f => path.join(RESOURCES, 'js', f));

    const ctx = await esbuild.context({
        entryPoints: jsEntries,
        bundle: true,
        sourcemap: true,
        minify: false,
        outdir: path.join(PUBLIC, 'js')
    });

    // Watch JS changes via chokidar instead
    const jsWatcher = chokidar.watch(path.join(RESOURCES, 'js'));
    jsWatcher.on('change', async filePath => {
        console.log('[JS] File changed:', filePath);

        try {
            await ctx.rebuild(); // rebuild JS bundle
            console.log('[JS HMR] JS rebuilt');

            // update manifest
            const file = path.basename(filePath);
            manifest[`js/${file}`] = `js/${file}`; // dev: no hash
            fs.writeFileSync(manifestPath, JSON.stringify(manifest, null, 2));

            // broadcast reload
            broadcastReload('js');
        } catch (err) {
            console.error('[JS HMR] Rebuild failed:', err);
        }
    });

    server = await ctx.serve({
        servedir: PUBLIC,
        port: PORT,
        host: 'localhost'
    });

    console.log(`⚡ JS dev server running at http://localhost:${server.port}`);
    console.log(`🌐 Live reload WS running at ws://localhost:${WS_PORT}`);
}

// --- Watch CSS for changes ---
chokidar.watch(path.join(RESOURCES, 'css')).on('change', async (filePath) => {
    const file = path.basename(filePath);
    await buildCSS(file);
});

// --- Initial CSS build ---
rebuildAllCSS().then(() => {
    startJSDevServer().catch(err => console.error(err));
});