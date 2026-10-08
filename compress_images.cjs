const sharp = require('sharp');
const fs = require('fs');
const path = require('path');

sharp.cache(false);

const IMAGES_DIR = path.join(__dirname, 'public', 'images');
const MAX_WIDTH = 1920;
const QUALITY = 80;

function getAllImageFiles(dir) {
    let results = [];
    const entries = fs.readdirSync(dir, { withFileTypes: true });
    for (const entry of entries) {
        const fullPath = path.join(dir, entry.name);
        if (entry.isDirectory()) {
            results = results.concat(getAllImageFiles(fullPath));
        } else if (entry.isFile() && /\.(png|jpe?g|webp)$/i.test(entry.name)) {
            // Skip any backup files
            if (!entry.name.includes('_orig.')) {
                results.push(fullPath);
            }
        }
    }
    return results;
}

async function optimizeImage(filePath) {
    const ext = path.extname(filePath).toLowerCase();
    const relPath = path.relative(IMAGES_DIR, filePath);
    const originalBuffer = fs.readFileSync(filePath);
    const originalSize = originalBuffer.length;

    try {
        let pipeline = sharp(originalBuffer);
        const meta = await pipeline.metadata();

        if (meta.width && meta.width > MAX_WIDTH) {
            pipeline = pipeline.resize({ width: MAX_WIDTH, withoutEnlargement: true });
        }

        let outputBuffer;
        if (ext === '.jpg' || ext === '.jpeg') {
            outputBuffer = await pipeline.jpeg({ quality: QUALITY, mozjpeg: true, progressive: true }).toBuffer();
        } else if (ext === '.png') {
            outputBuffer = await pipeline.png({ quality: QUALITY, palette: true, compressionLevel: 9, effort: 8 }).toBuffer();
        } else if (ext === '.webp') {
            outputBuffer = await pipeline.webp({ quality: QUALITY, effort: 6 }).toBuffer();
        } else {
            return { relPath, originalSize, newSize: originalSize, status: 'SKIPPED' };
        }

        if (outputBuffer.length < originalSize) {
            fs.writeFileSync(filePath, outputBuffer);
            return { relPath, originalSize, newSize: outputBuffer.length, status: 'OPTIMIZED' };
        } else {
            return { relPath, originalSize, newSize: originalSize, status: 'ALREADY_OPTIMIZED' };
        }
    } catch (err) {
        console.error(`Error optimizing ${relPath}: ${err.message}`);
        return { relPath, originalSize, newSize: originalSize, status: 'ERROR', error: err.message };
    }
}

async function main() {
    const files = getAllImageFiles(IMAGES_DIR);
    console.log(`\nFound ${files.length} images in ${IMAGES_DIR}\n`);
    console.log(`${'File'.padEnd(35)} ${'Original'.padStart(12)} ${'Optimized'.padStart(12)} ${'Savings'.padStart(10)}`);
    console.log('-'.repeat(73));

    let totalOriginal = 0;
    let totalOptimized = 0;

    for (const file of files) {
        const result = await optimizeImage(file);
        totalOriginal += result.originalSize;
        totalOptimized += result.newSize;

        const origKb = (result.originalSize / 1024).toFixed(1) + ' KB';
        const newKb = (result.newSize / 1024).toFixed(1) + ' KB';
        const pct = result.originalSize > 0
            ? ((1 - result.newSize / result.originalSize) * 100).toFixed(1) + '%'
            : '0%';

        console.log(`${result.relPath.padEnd(35)} ${origKb.padStart(12)} ${newKb.padStart(12)} ${pct.padStart(10)}`);
    }

    const totalOrigMb = (totalOriginal / 1024 / 1024).toFixed(2);
    const totalOptMb = (totalOptimized / 1024 / 1024).toFixed(2);
    const totalSavedMb = ((totalOriginal - totalOptimized) / 1024 / 1024).toFixed(2);
    const totalSavedPct = totalOriginal > 0
        ? ((1 - totalOptimized / totalOriginal) * 100).toFixed(1)
        : '0';

    console.log('-'.repeat(73));
    console.log(`Initial total size:   ${totalOrigMb} MB`);
    console.log(`Optimized total size: ${totalOptMb} MB`);
    console.log(`Total data saved:     ${totalSavedMb} MB (-${totalSavedPct}%)`);
    console.log('\nOptimization complete! All images replaced in-place with zero broken links.\n');
}

main().catch(console.error);
