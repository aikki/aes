<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class Run extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'run';

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


        $files = ['aes0.5x.bmp','aes.bmp','aes2x.bmp'];

        $repeats = 1;
        $time = time();
        $csv_file = fopen("aes.$time.$repeats.csv", 'w');


        $secret_key = 'WSK2020';
        $secret_iv = '2020WSK';

        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        $methods = [
            "AES-256-ECB" => '',
            "AES-256-CBC" => $iv,
            "AES-256-OFB" => $iv,
            "AES-256-CFB" => $iv,
            "AES-256-CTR" => $iv
        ];

        $mess = openssl_random_pseudo_bytes(95);


        $image = imagecreatefrompng('images/aes2x.png');
        var_dump($image);


        foreach ($methods as $encrypt_method => $iv) {
            $this->info($encrypt_method);
            $fields = [$encrypt_method];
            foreach ($files as $f) {
                $this->comment('  '.$f);
                $image = file_get_contents("images/$f");

                file_put_contents("images/$f.2.bmp", $image);
    
                $start=hrtime(true); 
                for ($i = 0; $i < $repeats; $i++) {
                    $enc = openssl_encrypt($image, $encrypt_method, $key, 0, $iv);
                }
                $end=hrtime(true);

                $fields[] = ($end-$start)/$repeats;

                $start=hrtime(true); 
                for ($i = 0; $i < $repeats; $i++) {
                    $enc = openssl_encrypt($image, $encrypt_method, $key, 0, $iv);
                }
                $end=hrtime(true); 

                $fields[] = ($end-$start)/$repeats;
        
                $this->info('  Encryption: '.(($end-$start)/$repeats));
        
                file_put_contents("images/$f.$encrypt_method.enc", $enc);

                $enc = substr_replace($enc, '1234123412341234123412341234123412341234123412341234123412341234123412341234123412341234123412341234123412341234123412341234123412341234123412341234123412341234123412341234123412341234123412341234123412341234123412341234123412341234123412341234123412341234', 127*1024, 256);

                $start=hrtime(true); 
                for ($i = 0; $i < $repeats; $i++) {
                    $dec = openssl_decrypt($enc, $encrypt_method, $key, 0, $iv);
                }
                $end=hrtime(true);

                $fields[] = ($end-$start)/$repeats;

                $start=hrtime(true); 
                for ($i = 0; $i < $repeats; $i++) {
                    $dec = openssl_decrypt($enc, $encrypt_method, $key, 0, $iv);
                }
                $end=hrtime(true); 

                $fields[] = ($end-$start)/$repeats;

                $this->info('  Decryption: '.(($end-$start)/$repeats));
        
                file_put_contents("images/$f.dec.$encrypt_method.bmp", $dec);
                $this->newLine();
            }
            fputcsv($csv_file, $fields);
        }
        fclose($csv_file);
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
