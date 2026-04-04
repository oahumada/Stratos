#!/usr/bin/env node
/**
 * enrich-changelog.cjs
 *
 * Runs after `standard-version` to expand the latest release section in
 * CHANGELOG.md with the full commit body (multi-line description).
 *
 * Usage (called automatically from release.sh):
 *   node scripts/enrich-changelog.cjs
 */

const { execSync } = require('child_process');
const fs = require('fs');
const path = require('path');

const CHANGELOG_PATH = path.join(__dirname, '..', 'CHANGELOG.md');

function getCommitBody(hash) {
    try {
        return execSync(`git log -1 --format="%b" ${hash}`, { encoding: 'utf8' }).trim();
    } catch {
        return '';
    }
}

function formatBody(body) {
    if (!body) return [];
    const rawLines = body
        .split('\n')
        .filter(l => !l.trim().startsWith('Co-authored-by:') && !l.trim().startsWith('Signed-off-by:'));

    const result = [];
    for (const raw of rawLines) {
        const trimmed = raw.trim();
        if (!trimmed) continue;

        if (trimmed.startsWith('- ')) {
            // Bullet line
            result.push(`  ${trimmed}`);
        } else if (raw.match(/^\S/) || raw.match(/^[A-Z]/)) {
            // Section header (not indented, starts with uppercase or no leading space)
            result.push(`  **${trimmed}**`);
        } else if (result.length && result[result.length - 1].startsWith('  - ')) {
            // Continuation of previous bullet — append to last line
            result[result.length - 1] += ' ' + trimmed;
        } else {
            result.push(`  ${trimmed}`);
        }
    }
    return result;
}

function enrichChangelog() {
    let content = fs.readFileSync(CHANGELOG_PATH, 'utf8');

    // Find the latest release block (between first and second "## [")
    const firstH2 = content.indexOf('\n## [');
    if (firstH2 === -1) {
        console.log('enrich-changelog: no release sections found, skipping.');
        return;
    }
    const secondH2 = content.indexOf('\n## [', firstH2 + 1);
    const latestBlock = secondH2 === -1
        ? content.slice(firstH2)
        : content.slice(firstH2, secondH2);

    // Extract all commit hashes from the latest block
    // Format: ([shortHash](https://...commit/fullHash))
    const hashRe = /\[([a-f0-9]{7})\]\(https?:\/\/[^\)]+\/commit\/([a-f0-9]+)\)/g;
    const hashes = new Map();
    let m;
    while ((m = hashRe.exec(latestBlock)) !== null) {
        if (!hashes.has(m[1])) hashes.set(m[1], m[2]);
    }

    if (!hashes.size) {
        console.log('enrich-changelog: no commit hashes found in latest block, skipping.');
        return;
    }

    // Process line-by-line to insert body after matching lines
    const lines = latestBlock.split('\n');
    const resultLines = [];
    let enrichedCount = 0;

    for (const line of lines) {
        resultLines.push(line);

        // Check if this line references a known commit hash
        for (const [short, full] of hashes) {
            if (line.includes(`[${short}]`) && line.match(/^\* /)) {
                // Check not already enriched (next resultLines entry would be "  - ")
                const lastIdx = resultLines.length - 1;
                const alreadyEnriched = resultLines[lastIdx - 1]?.startsWith('  - ');
                if (alreadyEnriched) break;

                const body = getCommitBody(full);
                const bodyLines = formatBody(body);
                if (bodyLines.length) {
                    bodyLines.forEach(bl => resultLines.push(bl));
                    enrichedCount++;
                }
                break;
            }
        }
    }

    if (!enrichedCount) {
        console.log('enrich-changelog: nothing to enrich (bodies already present or empty).');
        return;
    }

    const enrichedBlock = resultLines.join('\n');
    const newContent = secondH2 === -1
        ? content.slice(0, firstH2) + enrichedBlock
        : content.slice(0, firstH2) + enrichedBlock + content.slice(secondH2);

    fs.writeFileSync(CHANGELOG_PATH, newContent, 'utf8');
    console.log(`enrich-changelog: enriched ${enrichedCount} commit entries in CHANGELOG.md ✅`);
}

enrichChangelog();

