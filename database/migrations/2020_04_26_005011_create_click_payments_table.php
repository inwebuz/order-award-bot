<?php

use App\Payment\Click\Models\PaymentsStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClickPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('click_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('status', 50)->default(PaymentsStatus::INPUT);
            $table->string('status_note', 250)->nullable();
            $table->timestamp('created')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('modified')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('currency', 3)->default('UZB');
            $table->decimal('total', 10, 2)->default(0);
            $table->decimal('amount', 10, 2)->default(0);
            $table->decimal('delivery', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->string('card_token', 250)->nullable();
            $table->string('token', 50)->nullable();
            $table->string('phone_number', 50)->nullable();
            $table->string('merchant_trans_id', 200)->nullable();
            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('click_payments');
    }
}

/*CREATE TABLE `click_payments` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `status` varchar(50) NOT NULL,
  `status_note` varchar(250) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `currency` varchar(3) NOT NULL DEFAULT 'UZB',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `delivery` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax` decimal(10,2) NOT NULL DEFAULT '0.00',
  `description` text NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `payment_id` int(11) DEFAULT NULL,
  `card_token` varchar(250) DEFAULT NULL,
  `token` varchar(50) NOT NULL,
  `phone_number` varchar(50) DEFAULT NULL,
  `merchant_trans_id` varchar(200) DEFAULT NULL,
  `note` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `click_payments`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `click_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;
*/
