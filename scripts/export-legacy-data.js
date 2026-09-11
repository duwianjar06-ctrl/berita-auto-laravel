#!/usr/bin/env node

/**
 * Read-only legacy export for Berita Auto's Upstash Redis persistence.
 *
 * Required environment variables:
 *   UPSTASH_REDIS_REST_URL
 *   UPSTASH_REDIS_REST_TOKEN
 * Optional:
 *   LEGACY_EXPORT_OUTPUT (defaults to legacy-export.json)
 *
 * The script never writes to Redis and never prints secret values.
 * It scans keys, reads JSON values, and keeps objects that look like article
 * records or arrays of article records. The resulting shape is accepted by
 * `php artisan berita-auto:import`.
 */

import fs from 'node:fs/promises';

const url = String(process.env.UPSTASH_REDIS_REST_URL || '').replace(/\/$/, '');
const token = String(process.env.UPSTASH_REDIS_REST_TOKEN || '');
const output = process.env.LEGACY_EXPORT_OUTPUT || 'legacy-export.json';

if (!url || !token) {
  throw new Error('UPSTASH_REDIS_REST_URL and UPSTASH_REDIS_REST_TOKEN are required.');
}

async function command(path) {
  const response = await fetch(`${url}/${path}`, {
    headers: { Authorization: `Bearer ${token}` },
  });
  if (!response.ok) throw new Error(`Upstash request failed: HTTP ${response.status}`);
  return response.json();
}

function asArticle(value) {
  if (!value || typeof value !== 'object' || Array.isArray(value)) return null;
  const title = String(value.title ?? '').trim();
  const sourceUrl = String(value.sourceUrl ?? value.source_url ?? value.url ?? '').trim();
  if (!title || !sourceUrl) return null;
  return value;
}

function collectArticles(value) {
  const rows = Array.isArray(value)
    ? value
    : Array.isArray(value?.articles)
      ? value.articles
      : [value];
  return rows.map(asArticle).filter(Boolean);
}

const articles = [];
let cursor = '0';
let scannedKeys = 0;

while (true) {
  const scan = await command(`scan/${encodeURIComponent(cursor)}`);
  const result = scan.result || [];
  const nextCursor = String(result[0] ?? '0');
  const keys = Array.isArray(result[1]) ? result[1] : [];
  scannedKeys += keys.length;

  for (const key of keys) {
    const item = await command(`get/${encodeURIComponent(key)}`);
    if (typeof item.result !== 'string' || !item.result.trim()) continue;
    try {
      articles.push(...collectArticles(JSON.parse(item.result)));
    } catch {
      // Non-JSON Redis values are intentionally ignored.
    }
  }

  cursor = nextCursor;
  if (cursor === '0') break;
}

const seen = new Set();
const unique = articles.filter((article) => {
  const fingerprint = String(article.fingerprint || '').trim() ||
    `url:${String(article.sourceUrl ?? article.source_url ?? article.url).trim().toLowerCase()}`;
  if (seen.has(fingerprint)) return false;
  seen.add(fingerprint);
  return true;
});

await fs.writeFile(output, JSON.stringify({ articles: unique }, null, 2) + '\n', 'utf8');
console.log(JSON.stringify({ output, scannedKeys, articles: unique.length, readOnly: true }, null, 2));
