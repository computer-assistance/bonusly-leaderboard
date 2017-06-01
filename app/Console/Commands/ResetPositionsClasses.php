<?php

namespace App\Console\Commands;



use Illuminate\Console\Command;
use App\Models\Position;
use Response;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;

class ResetPositionsClasses extends Command
{
    /**
     * A friendly name of the command that will be used when loggin messages.
     *
     * @var string
     */
    protected $command_title = 'Set giver and receiver Positions classes';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pos:rstclsss';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command resets positions classes';

    /**
    * Create a new command instance.
    *
    * @return void
    */


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        echo "hotwire()";
    }

}








