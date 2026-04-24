// tools/builder/dev.js
const esbuild = require('esbuild');
const chokidar = require('chokidar');
const fs = require('fs');
const path = require('path');
const WebSocket = require('ws');

const ROOT = path.resolve(__dirname, '../../');
const RESOURCES = path.join(ROOT, 'resources');
const PUBLIC = path.join(ROOT, 'public');

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
    const full = path.join(PUBLIC, dir);
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
    const filePath = path.join(RESOURCES, 'css', 'app.css');
    const outPath = path.join(PUBLIC, 'css', 'app.css');
    const content = resolveCssImports(fs.readFileSync(filePath, 'utf-8'), filePath)
        .replace(/\s+/g, ' ')
        .trim();

    fs.writeFileSync(outPath, content);
    manifest['css/app.css'] = 'css/app.css';
    fs.writeFileSync(manifestPath, JSON.stringify(manifest, null, 2));

    broadcastReload('css', 'css/app.css');
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
    const sourceDir = path.join(RESOURCES, 'templates');
    const outputDir = path.join(PUBLIC, 'templates');

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
    const entry = path.join(RESOURCES, 'js', 'main.js');
    const outDir = path.join(PUBLIC, 'js');

    const ctx = await esbuild.context({
        entryPoints: [entry],
        bundle: true,
        sourcemap: true,
        minify: false,
        outfile: path.join(outDir, 'main.js')
    });

    await ctx.rebuild();
    manifest['js/main.js'] = 'js/main.js';
    fs.writeFileSync(manifestPath, JSON.stringify(manifest, null, 2));
    broadcastReload('js', 'js/main.js');
    console.log('[HMR] JS built: main.js');

    chokidar.watch(path.join(RESOURCES, 'js')).on('change', async () => {
        try {
            await ctx.rebuild();
            manifest['js/main.js'] = 'js/main.js';
            fs.writeFileSync(manifestPath, JSON.stringify(manifest, null, 2));
            broadcastReload('js', 'js/main.js');
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

chokidar.watch(path.join(RESOURCES, 'css')).on('change', async () => {
    await buildCSS();
});

chokidar.watch(path.join(RESOURCES, 'templates')).on('change', async () => {
    await buildTemplates();
    broadcastReload('js', 'js/main.js');
});

chokidar.watch(path.join(RESOURCES, 'index.html')).on('change', async () => {
    await buildIndexHtml();
    broadcastReload('js', 'js/main.js');
});

buildCSS().then(buildTemplates).then(buildIndexHtml).then(buildJS).catch(console.error);
