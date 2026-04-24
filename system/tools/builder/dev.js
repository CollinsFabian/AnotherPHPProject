const esbuild = require('esbuild');
const chokidar = require('chokidar');
const fs = require('fs');
const path = require('path');
const WebSocket = require('ws');

const ROOT = path.resolve(__dirname, '../../../');
const RESOURCES = path.join(ROOT, 'resources');
const RESOURCE_ASSETS = path.join(RESOURCES, 'assets');
const PUBLIC = path.join(ROOT, 'public');
const ASSETS = path.join(PUBLIC, 'assets');

const PORT = 3001;
const WS_PORT = 3002;

const wss = new WebSocket.Server({ port: WS_PORT });
function broadcastReload(type, file) {
    wss.clients.forEach(client => {
        if (client.readyState === 1) client.send(JSON.stringify({ type, file }));
    });
}

const manifestPath = path.join(PUBLIC, 'manifest.json');
let manifest = {};
if (fs.existsSync(manifestPath)) {
    manifest = JSON.parse(fs.readFileSync(manifestPath, 'utf-8'));
}

['js', 'css', 'templates'].forEach(dir => {
    const full = path.join(ASSETS, dir);
    if (!fs.existsSync(full)) fs.mkdirSync(full, { recursive: true });
});

function resolveCssImports(content, fromFile, seen = new Set()) {
    const importPattern = /@import\s+["'](.+?)["'];?/g;
    let result = content;
    let match;

    while ((match = importPattern.exec(content)) !== null) {
        const importPath = match[1];
        if (!importPath.startsWith('.')) continue;

        const resolvedPath = path.resolve(path.dirname(fromFile), importPath);
        if (seen.has(resolvedPath) || !fs.existsSync(resolvedPath)) {
            result = result.replace(match[0], '');
            continue;
        }

        seen.add(resolvedPath);
        const importedContent = fs.readFileSync(resolvedPath, 'utf-8');
        const flattenedContent = resolveCssImports(importedContent, resolvedPath, seen);
        result = result.replace(match[0], flattenedContent);
    }

    return result;
}

async function buildCSS() {
    const filePath = path.join(RESOURCE_ASSETS, 'css', 'app.css');
    const outPath = path.join(ASSETS, 'css', 'app.css');
    const content = resolveCssImports(fs.readFileSync(filePath, 'utf-8'), filePath)
        .replace(/\s+/g, ' ')
        .trim();

    fs.writeFileSync(outPath, content);
    manifest['assets/css/app.css'] = 'assets/css/app.css';
    fs.writeFileSync(manifestPath, JSON.stringify(manifest, null, 2));

    broadcastReload('css', 'assets/css/app.css');
    console.log('[HMR] CSS updated: app.css');
}

function copyDirectory(source, destination) {
    if (!fs.existsSync(source)) return;

    fs.mkdirSync(destination, { recursive: true });

    for (const entry of fs.readdirSync(source, { withFileTypes: true })) {
        const sourcePath = path.join(source, entry.name);
        const destinationPath = path.join(destination, entry.name);

        if (entry.isDirectory()) {
            copyDirectory(sourcePath, destinationPath);
            continue;
        }

        fs.copyFileSync(sourcePath, destinationPath);
    }
}

async function buildTemplates() {
    const sourceDir = path.join(RESOURCE_ASSETS, 'templates');
    const outputDir = path.join(ASSETS, 'templates');

    if (fs.existsSync(outputDir)) {
        fs.rmSync(outputDir, { recursive: true, force: true });
    }

    copyDirectory(sourceDir, outputDir);
}

async function buildIndexHtml() {
    const sourcePath = path.join(RESOURCES, 'index.html');
    const outputPath = path.join(PUBLIC, 'index.html');

    if (!fs.existsSync(sourcePath)) return;

    fs.copyFileSync(sourcePath, outputPath);
}

async function buildJS() {
    const entry = path.join(RESOURCE_ASSETS, 'js', 'main.js');
    const outDir = path.join(ASSETS, 'js');

    const ctx = await esbuild.context({
        entryPoints: [entry],
        bundle: true,
        sourcemap: true,
        minify: false,
        outfile: path.join(outDir, 'main.js')
    });

    await ctx.rebuild();
    manifest['assets/js/main.js'] = 'assets/js/main.js';
    fs.writeFileSync(manifestPath, JSON.stringify(manifest, null, 2));
    broadcastReload('js', 'assets/js/main.js');
    console.log('[HMR] JS built: main.js');

    chokidar.watch(path.join(RESOURCE_ASSETS, 'js')).on('change', async () => {
        try {
            await ctx.rebuild();
            manifest['assets/js/main.js'] = 'assets/js/main.js';
            fs.writeFileSync(manifestPath, JSON.stringify(manifest, null, 2));
            broadcastReload('js', 'assets/js/main.js');
            console.log('[HMR] JS rebuilt: main.js');
        } catch (err) {
            console.error('[HMR] JS rebuild failed:', err);
        }
    });

    const server = await ctx.serve({
        servedir: PUBLIC,
        port: PORT,
        host: 'localhost'
    });

    console.log(`Dev server running at http://localhost:${server.port}`);
    console.log(`Live reload WS at ws://localhost:${WS_PORT}`);
}

chokidar.watch(path.join(RESOURCE_ASSETS, 'css')).on('change', async () => {
    await buildCSS();
});

chokidar.watch(path.join(RESOURCE_ASSETS, 'templates')).on('change', async () => {
    await buildTemplates();
    broadcastReload('js', 'assets/js/main.js');
});

chokidar.watch(path.join(RESOURCES, 'index.html')).on('change', async () => {
    await buildIndexHtml();
    broadcastReload('js', 'assets/js/main.js');
});

buildCSS().then(buildTemplates).then(buildIndexHtml).then(buildJS).catch(console.error);
