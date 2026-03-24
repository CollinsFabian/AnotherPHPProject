// tools/builder/build.js
const esbuild = require('esbuild');
const fs = require('fs');
const path = require('path');
const crypto = require('crypto');
const postcss = require('postcss');
const autoprefixer = require('autoprefixer');

const isProd = process.argv.includes('--prod');

const ROOT = path.resolve(__dirname, '../../');
const RESOURCES = path.join(ROOT, 'resources');
const PUBLIC = path.join(ROOT, 'public');
const manifest = {};


['js', 'css'].forEach(dir => {
    const full = path.join(PUBLIC, dir);
    if (!fs.existsSync(full)) fs.mkdirSync(full, { recursive: true });
});

// Utility: hash
function hash(content) {
    return crypto.createHash('md5').update(content).digest('hex').slice(0, 8);
}

// Utility: write file with optional hash
function writeFile(type, name, content) {
    let filename = `${name}.${type}`;
    let outputPath = path.join(PUBLIC, type, filename);

    fs.writeFileSync(outputPath, content);

    if (isProd) {
        const h = hash(content);
        const hashedName = `${name}.${h}.${type}`;
        const hashedPath = path.join(PUBLIC, type, hashedName);

        fs.renameSync(outputPath, hashedPath);
        filename = hashedName;
    }

    manifest[`${type}/${name}.${type}`] = `${type}/${filename}`;
}

// JS BUILD
async function buildJS() {
    const dir = path.join(RESOURCES, 'js');
    if (!fs.existsSync(dir)) return;

    const files = fs.readdirSync(dir).filter(f => f.endsWith('.js'));

    for (const file of files) {
        const name = file.replace('.js', '');

        const result = await esbuild.build({
            entryPoints: [path.join(dir, file)],
            bundle: true,
            minify: isProd,
            sourcemap: !isProd,
            write: false,
        });

        const content = result.outputFiles[0].text;

        writeFile('js', name, content);
    }
}

// CSS BUILD
async function buildCSS() {
    const dir = path.join(RESOURCES, 'css');
    if (!fs.existsSync(dir)) return;

    const files = fs.readdirSync(dir).filter(f => f.endsWith('.css'));

    for (const file of files) {
        const name = file.replace('.css', '');
        let content = fs.readFileSync(path.join(dir, file), 'utf-8');

        // PostCSS processing
        const result = await postcss([
            autoprefixer
        ]).process(content, { from: undefined });

        content = result.css;

        // Simple minify (optional upgrade later)
        if (isProd) content = content.replace(/\s+/g, ' ').trim();

        writeFile('css', name, content);
    }
}

// MAIN BUILD
async function build() {
    console.log(isProd ? 'Building (production)...' : 'Building (development)...');

    await buildJS();
    await buildCSS();

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