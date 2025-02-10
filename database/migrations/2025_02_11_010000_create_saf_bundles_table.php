<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSafBundlesTable extends Migration
{
    public function up()
    {
        Schema::create('saf_bundles', function (Blueprint $table) {
            $table->id();
            $table->string('saf_ref_id');
            $table->string('saf_desc');
            $table->string('saf_status');
            $table->string('saf_created_value');
            $table->string('msisdn');
            $table->string('product_name');
            $table->string('product_id');
            $table->timestamp('subscription_date')->nullable();
            $table->timestamp('txn_date')->nullable();
            $table->string('txn_status');
            $table->decimal('amount', 10, 2);
            $table->json('payload');
            $table->timestamp('expiry_date')->nullable();
            $table->string('saf_request_id');
            $table->string('saf_tran_ref');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('saf_bundles');
    }
}
