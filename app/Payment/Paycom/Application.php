<?php

namespace App\Payment\Paycom;

use App\PaycomTransaction;
use Illuminate\Support\Facades\Log;

class Application
{
    public $config;
    public $request;
    public $response;
    public $merchant;

    /**
     * Application constructor.
     * @param array $config configuration array with <em>merchant_id</em>, <em>login</em>, <em>keyFile</em> keys.
     */
    public function __construct($config)
    {
        $this->config   = $config;
        $this->request  = new Request();
        $this->response = new Response($this->request);
        $this->merchant = new Merchant($this->config);
    }

    /**
     * Authorizes session and handles requests.
     */
    public function run()
    {
        // authorize session
        $this->merchant->Authorize($this->request->id);

        // handle request
        switch ($this->request->method) {
            case 'CheckPerformTransaction':
                $this->CheckPerformTransaction();
                break;
            case 'CheckTransaction':
                $this->CheckTransaction();
                break;
            case 'CreateTransaction':
                $this->CreateTransaction();
                break;
            case 'PerformTransaction':
                $this->PerformTransaction();
                break;
            case 'CancelTransaction':
                $this->CancelTransaction();
                break;
            case 'ChangePassword':
                $this->ChangePassword();
                break;
            case 'GetStatement':
                $this->GetStatement();
                break;
            default:
                $this->response->error(
                    PaycomException::ERROR_METHOD_NOT_FOUND,
                    'Method not found.',
                    $this->request->method
                );
                break;
        }
    }

    private function CheckPerformTransaction()
    {
        $order = new Order($this->request->id);
        $order->find($this->request->params['account']);

        // validate parameters
        $order->validate($this->request->params);

        $transaction = new Transaction();
        $found       = $transaction->find($this->request->params);
        if ($found && ($found->model->state == Transaction::STATE_CREATED || $found->model->state == Transaction::STATE_COMPLETED)) {
            $this->response->error(
                PaycomException::ERROR_COULD_NOT_PERFORM,
                'There is other active/completed transaction for this order.'
            );
        }

        // if control is here, then we pass all validations and checks
        // send response, that order is ready to be paid.
        $this->response->send(['allow' => true]);
    }

    private function CheckTransaction()
    {
        $transaction = new Transaction();
        $found       = $transaction->find($this->request->params);
        if (!$found) {
            $this->response->error(
                PaycomException::ERROR_TRANSACTION_NOT_FOUND,
                'Transaction not found.'
            );
        }

        $this->response->send([
            'create_time'  => Format::datetime2timestamp($found->model->create_time),
            'perform_time' => $found->model->perform_time ? Format::datetime2timestamp($found->model->perform_time) : 0,
            'cancel_time'  => $found->model->cancel_time ? Format::datetime2timestamp($found->model->cancel_time) : 0,
            'transaction'  => (string)$found->model->id,
            'state'        => (int)$found->model->state,
            'receivers'    => null,
            'reason'       => isset($found->model->reason) ? 1 * $found->model->reason : null,
        ]);
    }

    private function CreateTransaction()
    {
        $order = new Order($this->request->id);
        $order->find($this->request->params['account']);

        // validate parameters
        $order->validate($this->request->params);

        $transaction = new Transaction();
        $found       = $transaction->find(['account' => $this->request->params['account']]);
        if ($found) {
            if (($found->model->state == Transaction::STATE_CREATED || $found->model->state == Transaction::STATE_COMPLETED)
                && $found->model->paycom_transaction_id != $this->request->params['id']) {
                $this->response->error(
                    PaycomException::ERROR_INVALID_ACCOUNT,
                    'There is other active/completed transaction for this order.'
                );
            }
        }

        $transaction = new Transaction();
        $found       = $transaction->find($this->request->params);
        if ($found) {
            if ($found->model->state != Transaction::STATE_CREATED) { // validate transaction state
                $this->response->error(
                    PaycomException::ERROR_COULD_NOT_PERFORM,
                    'Transaction found, but is not active.'
                );
            } elseif ($found->isExpired()) { // if transaction timed out, cancel it and send error
                $found->cancel(Transaction::REASON_CANCELLED_BY_TIMEOUT);
                $this->response->error(
                    PaycomException::ERROR_COULD_NOT_PERFORM,
                    'Transaction is expired.'
                );
            } else { // if transaction found and active, send it as response
                $this->response->send([
                    'create_time' => Format::datetime2timestamp($found->model->create_time),
                    'perform_time' => $found->model->perform_time ? Format::datetime2timestamp($found->model->perform_time) : 0,
                    'cancel_time'  => $found->model->cancel_time ? Format::datetime2timestamp($found->model->cancel_time) : 0,
                    'transaction' => (string)$found->model->id,
                    'state'       => (int)$found->model->state,
                    'receivers'   => $found->model->receivers,
                    'reason'       => isset($found->model->reason) ? 1 * $found->model->reason : null,
                ]);
            }
        } else { // transaction not found, create new one

            // validate new transaction time
            if (Format::timestamp2milliseconds(1 * $this->request->params['time']) - Format::timestamp(true) >= Transaction::TIMEOUT) {
                $this->response->error(
                    PaycomException::ERROR_INVALID_ACCOUNT,
                    PaycomException::message(
                        'С даты создания транзакции прошло ' . Transaction::TIMEOUT . 'мс',
                        'Tranzaksiya yaratilgan sanadan ' . Transaction::TIMEOUT . 'ms o`tgan',
                        'Since create time of the transaction passed ' . Transaction::TIMEOUT . 'ms'
                    ),
                    'time'
                );
            }

            // create new transaction
            // keep create_time as timestamp, it is necessary in response
            $transaction->model                         = new PaycomTransaction();
            $create_time                                = Format::timestamp(true);
            $transaction->model->paycom_transaction_id  = $this->request->params['id'];
            $transaction->model->paycom_time            = $this->request->params['time'];
            $transaction->model->paycom_time_datetime   = Format::timestamp2datetime($this->request->params['time']);
            $transaction->model->create_time            = Format::timestamp2datetime($create_time);
            $transaction->model->state                  = Transaction::STATE_CREATED;
            $transaction->model->amount                 = $this->request->amount;
            $transaction->model->order_id               = $this->request->account('order_id');
            $transaction->model->save(); // after save $transaction->id will be populated with the newly created transaction's id.

            // send response
            $this->response->send([
                'create_time' => $create_time,
                'perform_time' => 0,
                'cancel_time'  => 0,
                'transaction' => (string)$transaction->model->id,
                'state'       => $transaction->model->state,
                'receivers'   => null,
                'reason'       => isset($transaction->model->reason) ? 1 * $transaction->model->reason : null,
            ]);
        }
    }

    private function PerformTransaction()
    {
        $transaction = new Transaction();
        // search transaction by id
        $found = $transaction->find($this->request->params);

        // if transaction not found, send error
        if (!$found) {
            $this->response->error(PaycomException::ERROR_TRANSACTION_NOT_FOUND, 'Transaction not found.');
        }

        switch ($found->model->state) {
            case Transaction::STATE_CREATED: // handle active transaction
                if ($found->isExpired()) { // if transaction is expired, then cancel it and send error
                    $found->cancel(Transaction::REASON_CANCELLED_BY_TIMEOUT);
                    $this->response->error(
                        PaycomException::ERROR_COULD_NOT_PERFORM,
                        'Transaction is expired.'
                    );
                } else { // perform active transaction
                    $accountParams = ['order_id' => $found->model->order_id];
                    $order  = new Order($this->request->id);
                    $order->find($accountParams);
                    $order->changeState($order->model::STATUS_PAID);

                    $perform_time               = Format::timestamp(true);
                    $found->model->state        = Transaction::STATE_COMPLETED;
                    $found->model->perform_time = Format::timestamp2datetime($perform_time);
                    $found->model->save();

                    $this->response->send([
                        'transaction'  => (string)$found->model->id,
                        'perform_time' => $perform_time,
                        'state'        => (int)$found->model->state,
                    ]);
                }
                break;

            case Transaction::STATE_COMPLETED: // handle complete transaction
                $this->response->send([
                    'transaction'  => (string)$found->model->id,
                    'perform_time' => Format::datetime2timestamp($found->model->perform_time),
                    'state'        => (int)$found->model->state,
                ]);
                break;

            default:
                // unknown situation
                $this->response->error(
                    PaycomException::ERROR_COULD_NOT_PERFORM,
                    'Could not perform this operation.'
                );
                break;
        }
    }

    private function CancelTransaction()
    {
        $transaction = new Transaction();

        // search transaction by id
        $found = $transaction->find($this->request->params);

        // if transaction not found, send error
        if (!$found) {
            $this->response->error(PaycomException::ERROR_TRANSACTION_NOT_FOUND, 'Transaction not found.');
        }
        $accountParams = ['order_id' => $found->model->order_id];

        switch ($found->model->state) {
            // if already cancelled, just send it
            case Transaction::STATE_CANCELLED:
            case Transaction::STATE_CANCELLED_AFTER_COMPLETE:
                $this->response->send([
                    'transaction' => (string)$found->model->id,
                    'cancel_time' => Format::datetime2timestamp($found->model->cancel_time),
                    'state'       => (int)$found->model->state,
                ]);
                break;

            // cancel active transaction
            case Transaction::STATE_CREATED:
                // cancel transaction with given reason
                $found->cancel(1 * $this->request->params['reason']);
                // after $found->cancel(), cancel_time and state properties populated with data

                // change order state to cancelled
                $order = new Order($this->request->id);
                $order->find($accountParams);
                $order->changeState($order->model::STATUS_CANCELLED);

                // send response
                $this->response->send([
                    'transaction' => (string)$found->model->id,
                    'cancel_time' => Format::datetime2timestamp($found->model->cancel_time),
                    'state'       => (int)$found->model->state,
                ]);
                break;

            case Transaction::STATE_COMPLETED:
                // find order and check, whether cancelling is possible this order
                $order = new Order($this->request->id);
                $order->find($accountParams);
                if ($order->allowCancel()) {
                    // cancel and change state to cancelled
                    $found->cancel(1 * $this->request->params['reason']);
                    // after $found->cancel(), cancel_time and state properties populated with data

                    $order->changeState($order->model::STATUS_CANCELLED_AFTER_PAYMENT);

                    // send response
                    $this->response->send([
                        'transaction' => (string)$found->model->id,
                        'cancel_time' => Format::datetime2timestamp($found->model->cancel_time),
                        'state'       => (int)$found->model->state,
                    ]);
                } else {
                    $this->response->error(
                        PaycomException::ERROR_COULD_NOT_CANCEL,
                        'Could not cancel transaction. Order is delivered/Service is completed.'
                    );
                }
                break;
        }
    }

    private function ChangePassword()
    {
        // validate, password is specified, otherwise send error
        if (!isset($this->request->params['password']) || !trim($this->request->params['password'])) {
            $this->response->error(PaycomException::ERROR_INVALID_ACCOUNT, 'New password not specified.', 'password');
        }

        // if current password specified as new, then send error
        if ($this->merchant->config['key'] == $this->request->params['password']) {
            $this->response->error(PaycomException::ERROR_INSUFFICIENT_PRIVILEGE, 'Insufficient privilege. Incorrect new password.');
        }

        // example implementation, that saves new password into file specified in the configuration
        if (!file_put_contents($this->config['keyFile'], $this->request->params['password'])) {
            $this->response->error(PaycomException::ERROR_INTERNAL_SYSTEM, 'Internal System Error.');
        }

        // if control is here, then password is saved into data store
        // send success response
        $this->response->send(['success' => true]);
    }

    private function GetStatement()
    {
        // validate 'from'
        if (!isset($this->request->params['from'])) {
            $this->response->error(PaycomException::ERROR_INVALID_ACCOUNT, 'Incorrect period.', 'from');
        }

        // validate 'to'
        if (!isset($this->request->params['to'])) {
            $this->response->error(PaycomException::ERROR_INVALID_ACCOUNT, 'Incorrect period.', 'to');
        }

        // validate period
        if (1 * $this->request->params['from'] >= 1 * $this->request->params['to']) {
            $this->response->error(PaycomException::ERROR_INVALID_ACCOUNT, 'Incorrect period. (from >= to)', 'from');
        }

        // get list of transactions for specified period
        $transaction  = new Transaction();
        $transactions = $transaction->report($this->request->params['from'], $this->request->params['to']);

        // send results back
        $this->response->send(['transactions' => $transactions]);
    }
}
