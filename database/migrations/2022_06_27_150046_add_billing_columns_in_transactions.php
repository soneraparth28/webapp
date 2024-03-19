<?php

use App\Models\Transactions;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBillingColumnsInTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string("invoice_name")->nullable();
            $table->string("invoice_company")->nullable();
            $table->string("invoice_address")->nullable();
            $table->string("invoice_city")->nullable();
            $table->string("invoice_zip")->nullable();
            $table->string("invoice_email")->nullable();
            $table->string("invoice_country")->nullable();
        });
        
        $trxs = Transactions::all();
        foreach ($trxs as $key => $item) {
            $user = User::where("id", $item->user_id)->first();
            if($user){
                $item->invoice_name = !blank($user->name) ? $user->name : NULL;
                $item->invoice_company = !blank($user->company) ? $user->company : NULL;
                $item->invoice_address = !blank($user->address) ? $user->address : NULL;
                $item->invoice_city = !blank($user->city) ? $user->city : NULL;
                $item->invoice_zip = !blank($user->zip) ? $user->zip : NULL;
                $item->invoice_email = !blank($user->email) ? $user->email : NULL;
                $item->invoice_country = !blank($user->countries_id) ? $user->country()->country_name : NULL;
                $item->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            //
        });
    }
}
