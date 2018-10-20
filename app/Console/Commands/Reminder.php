<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Http\Request;
use App\Http\Requests;

use DB;

class Reminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Reminder:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Event Reminder';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $controller = new \App\Http\Controllers\notification();
        $controller->reminder();
        // $current_datetime = date('Y-m-d H:i:s')."rim";
        // DB::table('tblcron_log')->insert(['log_data' => $current_datetime,
        //                                   'created_at' => date('Y-m-d H:i:s'),
        //                                   'updated_at' => date('Y-m-d H:i:s')]);     
    }
}