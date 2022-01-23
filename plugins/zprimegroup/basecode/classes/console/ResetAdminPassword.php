<?php namespace Zprimegroup\Basecode\Classes\Console;

use Backend\Models\User;
use Illuminate\Console\Command;

/**
 * Class ResetAdminPassword
 */
class ResetAdminPassword extends Command
{
    /**
     * The console command name.
     * @var string
     */
    protected $name = 'basecode:reset_admin_password';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Reset admin password to default value';

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
       $obUser = User::where('login', 'admin')->first();
       if (empty($obUser)) {
           return;
       }

       $obUser->password = 'admin';
       $obUser->password_confirmation = 'admin';
       $obUser->save();
    }
}
