<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class CacheStatsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'metrics:cache-stats {--org-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display cache statistics and monitoring information';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('📊 Metrics Cache Statistics');
        $this->newLine();

        // Get Redis connection
        $redis = Cache::store('redis')->connection();

        try {
            $info = $redis->info('stats');
            $memory = $redis->info('memory');
            $keyspace = $redis->info('keyspace');

            // Display basic stats
            $this->table(['Metric', 'Value'], [
                ['Total Connected Clients', $info['connected_clients'] ?? 'N/A'],
                ['Commands Processed', $info['total_commands_processed'] ?? 'N/A'],
                ['Keyspace Hits', $info['keyspace_hits'] ?? 'N/A'],
                ['Keyspace Misses', $info['keyspace_misses'] ?? 'N/A'],
                ['Memory Used', $memory['used_memory_human'] ?? 'N/A'],
                ['Memory Peak', $memory['used_memory_peak_human'] ?? 'N/A'],
            ]);

            // Calculate hit ratio
            $hits = $info['keyspace_hits'] ?? 0;
            $misses = $info['keyspace_misses'] ?? 0;
            $total = $hits + $misses;
            $ratio = $total > 0 ? round(($hits / $total) * 100, 2) : 0;

            $this->newLine();
            $this->info("Cache Hit Ratio: {$ratio}% ({$hits} hits, {$misses} misses)");

            // Display cached keys info
            $this->newLine();
            $this->info('📦 Cached Keys Information:');
            $this->displayCachedKeysInfo($redis);

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to fetch cache stats: {$e->getMessage()}");

            return self::FAILURE;
        }
    }

    /**
     * Display information about cached metric keys.
     */
    private function displayCachedKeysInfo($redis): void
    {
        try {
            $pattern = 'metrics_and_benchmarks_*';
            $keys = $redis->keys($pattern);
            $count = count((array) $keys);

            $this->line("  • Metric Cache Keys: {$count}");

            if ($count > 0 && $count <= 20) {
                $this->line('  Keys:');
                foreach ((array) $keys as $key) {
                    $ttl = $redis->ttl($key);
                    $size = strlen((string) $redis->get($key));
                    $this->line("    - {$key} (TTL: {$ttl}s, Size: {$size} bytes)");
                }
            } elseif ($count > 20) {
                $this->line("  (showing first 20 of {$count} keys)");
                $keys = array_slice((array) $keys, 0, 20);
                foreach ($keys as $key) {
                    $ttl = $redis->ttl($key);
                    $size = strlen((string) $redis->get($key));
                    $this->line("    - {$key} (TTL: {$ttl}s, Size: {$size} bytes)");
                }
            }

            $this->newLine();
            $this->comment('💡 Tip: Use `php artisan metrics:cache-refresh {org_id} --apply` to invalidate specific org cache');
            $this->comment('💡 Tip: Use `php artisan metrics:warm-cache` to pre-warm cache for all orgs');
        } catch (\Exception $e) {
            $this->warn("Could not display detailed key info: {$e->getMessage()}");
        }
    }
}
