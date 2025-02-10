<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSafBundlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('saf_bundles')) {
            Schema::create('saf_bundles', function (Blueprint $table) {
                $table->id();
                $table->string('saf_ref_id');
                $table->string('saf_desc');
                $table->string('saf_status');
                $table->string('saf_created_value');
                $table->string('msisdn');
                $table->string('product_name');
                $table->string('product_id');
                $table->timestamp('subscription_date');
                $table->timestamp('txn_date');
                $table->boolean('txn_status')->default(0);
                $table->decimal('amount', 10, 2);
                $table->json('payload');
                $table->timestamp('expiry_date');
                $table->string('saf_request_id');
                $table->string('saf_tran_ref');
                $table->timestamps();

                $table->index(['msisdn', 'txn_status']);
                $table->index(['saf_request_id', 'saf_tran_ref']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('saf_bundles');
    }
}
