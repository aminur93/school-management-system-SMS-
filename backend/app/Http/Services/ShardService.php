<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\DB;

class ShardService
{
    const TOTAL_SHARDS = 2;

    public static function connection(int $shardKey)
    {
        $shard = crc32($shardKey) % self::TOTAL_SHARDS;

        return $shard === 0
            ? DB::connection('mysql_shard_0')
            : DB::connection('mysql_shard_1');
    }
}