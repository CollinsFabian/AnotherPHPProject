const esbuild = require('esbuild');
const fs = require('fs');
const path = require('path');
const crypto = require('crypto');
const cssnano = require('cssnano');
const postcss = require('postcss');
const autoprefixer = require('autoprefixer');

const isProd = process.argv.includes('--prod');

const ROOT = path.resolve(__dirname, '../../../');
const RESOURCES = path.join(ROOT, 'resources');
const RESOURCE_ASSETS = path.join(RESOURCES, 'assets');
const PUBLIC = path.join(ROOT, 'public');
const ASSETS = path.join(PUBLIC, 'assets');
const manifest = {};


['js', 'css', 'templates'].forEach(dir => {
    const full = path.join(ASSETS, dir);
    if (!fs.existsSync(full)) fs.mkdirSync(full, { recursive: true });
});

// Utility: hash
function hash(content) {
    return crypto.createHash('md5').update(content).digest('hex').slice(0, 8);
}

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

// Utility: write file with optional hash
function writeFile(type, name, content) {
    let filename = `${name}.${type}`;
    let outputPath = path.join(ASSETS, type, filename);

    fs.writeFileSync(outputPath, content);

    if (isProd) {
        const h = hash(content);
        const hashedName = `${name}.${h}.${type}`;
        const hashedPath = path.join(ASSETS, type, hashedName);

        fs.renameSync(outputPath, hashedPath);
        filename = hashedName;
    }

    manifest[`assets/${type}/${name}.${type}`] = `assets/${type}/${filename}`;
}

// JS BUILD
async function buildJS() {
    const entryFile = path.join(RESOURCE_ASSETS, 'js', 'main.js');
    const result = await esbuild.build({
        entryPoints: [entryFile],
        bundle: true,
        minify: isProd,
        sourcemap: !isProd,
        write: false,
    });

    const file = result.outputFiles.find(f => f.path && f.path.endsWith('.js')) || result.outputFiles[0];
    const content = file.text;

    writeFile('js', 'main', content);
}

// CSS BUILD
async function buildCSS() {
    const dir = path.join(RESOURCE_ASSETS, 'css');
    if (!fs.existsSync(dir)) return;

    const filePath = path.join(dir, 'app.css');
    let content = fs.readFileSync(filePath, 'utf-8');
    content = resolveCssImports(content, filePath);

    const result = await postcss([
        autoprefixer,
        ...(isProd ? [cssnano] : [])
    ]).process(content, { from: undefined });

    content = result.css;
    writeFile('css', 'app', content);
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

    let html = fs.readFileSync(sourcePath, 'utf-8');

    // Only replace in production
    if (isProd) {
        Object.entries(manifest).forEach(([original, hashed]) => {
            html = html.replaceAll(original, hashed);
        });
    }

    fs.writeFileSync(outputPath, html);
}

// MAIN BUILD
async function build() {
    if (fs.existsSync(ASSETS)) {
        fs.rmSync(ASSETS, { recursive: true, force: true });
    }

    ['js', 'css', 'templates'].forEach(dir => {
        const full = path.join(ASSETS, dir);
        fs.mkdirSync(full, { recursive: true });
    });

    console.log(isProd ? 'Building (production)...' : 'Building (development)...');

    await buildJS();
    await buildCSS();
    await buildTemplates();
    await buildIndexHtml();

    // Write manifest
    fs.writeFileSync(
        path.join(PUBLIC, 'manifest.json'),
        JSON.stringify(manifest, null, 2)
    );

    console.log('✅ Build complete');
}

build().catch(err => {
    console.error(err);
    process.exit(1);
});
