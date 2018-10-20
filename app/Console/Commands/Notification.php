<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Http\Request;
use App\Http\Requests;
// namespace App\Http\Controllers;
use Illuminate\Routing\Controller as BaseController;

use DB;

class Notification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Notification:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Event Notification';

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
        // $controller = new \App\Http\Controllers\notification();
        // $controller->notification();
        // $current_datetime = date('Y-m-d H:i:s')."notify";
        // DB::table('tblcron_log')->insert(['log_data' => $current_datetime,
        //                                   'created_at' => date('Y-m-d H:i:s'),
        //                                   'updated_at' => date('Y-m-d H:i:s')]);     
    }
}