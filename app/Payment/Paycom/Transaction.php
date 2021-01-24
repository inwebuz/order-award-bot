<?php

namespace App\Payment\Paycom;

use App\Order as AppOrder;
use App\PaycomTransaction;

/**
 * Class Transaction
 *
 * Example MySQL table might look like to the following:
 *
 * CREATE TABLE `transactions` (
 *   `id` INT(11) NOT NULL AUTO_INCREMENT,
 *   `paycom_transaction_id` VARCHAR(25) NOT NULL COLLATE 'utf8_unicode_ci',
 *   `paycom_time` VARCHAR(13) NOT NULL COLLATE 'utf8_unicode_ci',
 *   `paycom_time_datetime` DATETIME NOT NULL,
 *   `create_time` DATETIME NOT NULL,
 *   `perform_time` DATETIME NULL DEFAULT NULL,
 *   `cancel_time` DATETIME NULL DEFAULT NULL,
 *   `amount` INT(11) NOT NULL,
 *   `state` TINYINT(2) NOT NULL,
 *   `reason` TINYINT(2) NULL DEFAULT NULL,
 *   `receivers` VARCHAR(500) NULL DEFAULT NULL COMMENT 'JSON array of receivers' COLLATE 'utf8_unicode_ci',
 *   `order_id` INT(11) NOT NULL,
 *
 *   PRIMARY KEY (`id`)
 * )
 *   COLLATE='utf8_unicode_ci'
 *   ENGINE=InnoDB
 *   AUTO_INCREMENT=1;
 *
 */
class Transaction
{
    /** Transaction expiration time in milliseconds. 43 200 000 ms = 12 hours. */
    const TIMEOUT = 43200000;

    const STATE_CREATED                  = 1;
    const STATE_COMPLETED                = 2;
    const STATE_CANCELLED                = -1;
    const STATE_CANCELLED_AFTER_COMPLETE = -2;

    const REASON_RECEIVERS_NOT_FOUND         = 1;
    const REASON_PROCESSING_EXECUTION_FAILED = 2;
    const REASON_EXECUTION_FAILED            = 3;
    const REASON_CANCELLED_BY_TIMEOUT        = 4;
    const REASON_FUND_RETURNED               = 5;
    const REASON_UNKNOWN                     = 10;

    /** @var \App\PaycomTransaction */
    public $model;

    /**
     * Cancels transaction with the specified reason.
     * @param int $reason cancelling reason.
     * @return void
     */
    public function cancel($reason)
    {
        $this->model->cancel_time = Format::timestamp2datetime(Format::timestamp());

        if ($this->model->state == self::STATE_COMPLETED) {
            // Scenario: CreateTransaction -> PerformTransaction -> CancelTransaction
            $this->model->state = self::STATE_CANCELLED_AFTER_COMPLETE;
        } else {
            // Scenario: CreateTransaction -> CancelTransaction
            $this->model->state = self::STATE_CANCELLED;
        }

        // set reason
        $this->model->reason = $reason;

        $this->model->save();
    }

    /**
     * Determines whether current transaction is expired or not.
     * @return bool true - if current instance of the transaction is expired, false - otherwise.
     */
    public function isExpired()
    {
        return $this->model->state == self::STATE_CREATED && abs(Format::datetime2timestamp($this->model->create_time) - Format::timestamp(true)) > self::TIMEOUT;
    }

    /**
     * Find transaction by given parameters.
     * @param mixed $params parameters
     * @return Transaction|Transaction[]
     * @throws PaycomException invalid parameters specified
     */
    public function find($params)
    {
        if (isset($params['id'])) {
            $paycomTransaction = PaycomTransaction::where('paycom_transaction_id', $params['id'])->first();
        } elseif (isset($params['account'], $params['account']['order_id'])) {
            $appOrder = AppOrder::find($params['account']['order_id']);
            if ($appOrder !== null) {
                $paycomTransaction = $appOrder->transactions()->whereIn('state', [Transaction::STATE_CREATED, Transaction::STATE_COMPLETED])->first();
            }
        } else {
            throw new PaycomException(
                $params['request_id'],
                'Parameter to find a transaction is not specified.',
                PaycomException::ERROR_INTERNAL_SYSTEM
            );
        }

        // if SQL operation succeeded, then try to populate instance properties with values
        if ($paycomTransaction !== null) {
            $this->model = $paycomTransaction;
            return $this;
        }

        // transaction not found, return null
        return null;

        // Possible features:
        // Search transaction by product/order id that specified in $params
        // Search transactions for a given period of time that specified in $params
    }

    /**
     * Gets list of transactions for the given period including period boundaries.
     * @param int $from_date start of the period in timestamp.
     * @param int $to_date end of the period in timestamp.
     * @return array list of found transactions converted into report format for send as a response.
     */
    public function report($from_date, $to_date)
    {
        $from_date = Format::timestamp2datetime($from_date);
        $to_date   = Format::timestamp2datetime($to_date);

        $transactions = PaycomTransaction::where([['paycom_time_datetime', '>=', $from_date], ['paycom_time_datetime', '<=', $to_date]])->get();

        foreach ($transactions as $transaction) {
            $result[] = [
                'id'           => $transaction->paycom_transaction_id, // paycom transaction id
                'time'         => 1 * $transaction->paycom_time, // paycom transaction timestamp as is
                'amount'       => 1 * $transaction->amount,
                'account'      => [
                    'order_id' => 1 * $transaction->order_id, // account parameters to identify client/order/service
                    // ... additional parameters may be listed here, which are belongs to the account
                ],
                'create_time'  => Format::datetime2timestamp($transaction->create_time),
                'perform_time' => Format::datetime2timestamp($transaction->perform_time),
                'cancel_time'  => Format::datetime2timestamp($transaction->cancel_time),
                'transaction'  => $transaction->id,
                'state'        => 1 * $transaction->state,
                'reason'       => isset($transaction->reason) ? 1 * $transaction->reason : null,
                'receivers'    => isset($transaction->receivers) ? json_decode($transaction->receivers, true) : null,
            ];
        }

        return $result;

    }
}
