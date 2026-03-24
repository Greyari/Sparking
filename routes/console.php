<?php

use Illuminate\Support\Facades\Schedule;

/*
 * Scheduler untuk mengecek slot parkir kosong setiap menit.
 * Jika ada slot tersedia, email notifikasi otomatis dikirim
 * ke user yang telah mendaftar fitur pemberitahuan.
 *
 * Untuk development : php artisan schedule:work
 * Untuk production  : tambahkan cron di server
 *   * * * * * cd /path-project && php artisan schedule:run >> /dev/null 2>&1
 */

Schedule::command('parkir:cek-slot-kosong')->everyMinute();
