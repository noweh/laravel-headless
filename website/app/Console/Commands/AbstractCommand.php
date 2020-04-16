<?php

namespace App\Console\Commands;

use Cache;
use Illuminate\Console\Command;

abstract class AbstractCommand extends Command
{
    protected $file_process;

    protected function startProcess()
    {
        if (app()->environment('local')) {
            return true;
        }

        if (!file_exists(storage_path().$this->file_process) ||
            (time() - filemtime(storage_path().$this->file_process)) > 900// 15 minutes
        ) {
            $fp = fopen(storage_path().$this->file_process, 'w');
            if ($fp) {
                fwrite($fp, (string) time());
                fclose($fp);
                return true;
            }
        }
        return false;
    }

    protected function endProcess()
    {
        if (Cache::getDefaultDriver() == 'redis') {
            // Remove collections cache
            Cache::tags('collections')->flush();
        }

        if (file_exists(storage_path() . $this->file_process)) {
            @unlink(storage_path() . $this->file_process);
        }
    }
}
