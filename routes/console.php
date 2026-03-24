<?php

use Illuminate\Support\Facades\Schedule;

// untuk cek slot kosong setiap menit
Schedule::command('parkir:cek-slot-kosong')->everyMinute();
