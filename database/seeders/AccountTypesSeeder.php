<?php

namespace Database\Seeders;

use App\Models\AccountGroup;
use App\Models\LedgerAccount;
use Illuminate\Database\Seeder;

class AccountTypesSeeder extends Seeder
{
    public function run()
    {
        // This will populate the account_type field for existing records
        // based on their account group's nature

        LedgerAccount::whereNull('account_type')->chunk(100, function ($accounts) {
            foreach ($accounts as $account) {
                if ($account->accountGroup) {
                    $account->update(['account_type' => $account->accountGroup->nature]);
                }
            }
        });
    }
}
