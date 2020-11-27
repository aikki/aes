<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class Run2 extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'run2';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // dump(array_filter(openssl_get_cipher_methods(), fn($m) => strstr($m, 'aes')));


        $repeats = 1;

        $secret_key = 'WSK2020';
        $secret_iv = '2020WSK';

        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        $encrypt_method = 'AES-256-CBC';

        $image = 'TESTOWY TEKST 123';
        $this->info($image);
        $enc = openssl_encrypt($image, $encrypt_method, $key, 0, $iv);
        $this->info($enc);
        $dec = openssl_decrypt($enc, $encrypt_method, $key, 0, $iv);
        $this->info($dec);
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
