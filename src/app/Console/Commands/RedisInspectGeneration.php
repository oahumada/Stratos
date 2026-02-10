<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class RedisInspectGeneration extends Command
{
    protected $signature = 'redis:inspect-generation {organization_id} {generation_id} {--scenario_id=} {--delete : Delete matching keys}';
    protected $description = 'List Redis keys for a generation buffer and optionally delete them';

    public function handle()
    {
        $org = (int) $this->argument('organization_id');
        $gen = (int) $this->argument('generation_id');
        $scenario = $this->option('scenario_id') ? (int) $this->option('scenario_id') : null;
        $delete = (bool) $this->option('delete');

        $prefix = 'app:scenario_planning:org:' . $org;
        if ($scenario) {
            $pattern = "{$prefix}:scenario:{$scenario}:generation:{$gen}:*";
        } else {
            $pattern = "{$prefix}:*generation:{$gen}:*";
        }

        $this->info("Searching keys by pattern: {$pattern}");

        // Use scan to avoid blocking
        $it = null;
        $found = 0;
        do {
            $res = Redis::scan($it, ['MATCH' => $pattern, 'COUNT' => 1000]);
            if ($res === false) break;
            list($it, $keys) = $res;
            foreach ($keys as $k) {
                $found++;
                $len = Redis::llen($k);
                $this->line("- {$k} (len={$len})");
                if ($delete) {
                    Redis::del($k);
                    $this->line("  deleted");
                }
            }
        } while ($it != 0 && $it !== '0');

        $this->info("Found: {$found} keys");

        return 0;
    }
}
