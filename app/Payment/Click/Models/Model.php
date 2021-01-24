<?php

namespace App\Payment\Click\Models;

use Illuminate\Support\Facades\Log;
use \PDO;

/**
 * @name Model class, this can help you for connecting, reading, writing, updating the payments
 *
 * @example
 *      $model   = new Model();
 *      $payment = $model->find_by_token('aaaa-bbbb-cccc-ddddddddd');
 */
class Model
{
    /** @var $params array-like, it has need included the database configurations */
    private $params;

    /** @var $conn PDO object, it will be helpfull for connect to database */
    private $conn;

    /** @var $configs array-like */
    private $configs;

    /**
     * Payments constructor
     * @param $params array-like, the db configuration
     */
    public function __construct($params)
    {
        // set db params
        $this->params = $params['db'];

        // connection to database
        $this->conn = $this->connect();

        // set the configurations
        $this->configs = null;
        if (isset($params['configs'])) {
            $this->configs = $params['configs'];
        }
    }

    /**
     * @name \App\Payment\Click\Models\Model::connect method, the mean method for connection to database and this
     * called in contructor
     * @return PDO object
     */
    private function connect()
    {
        // make the PDO connection object
        $conn = new PDO($this->params['dsn'], $this->params['username'], $this->params['password']);

        // set attributes
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // return PDO object
        return $conn;
    }

    /**
     * @name \App\Payment\Click\Models\Model::find_by_id, this method can find the payment data by id
     * @param $payment_id integer
     * @return array-like
     *
     * @example:
     *      $model = new Model();
     *      $payment = $model->find_by_id(1111);
     */
    public function find_by_id($payment_id)
    {
        // make sql query
        $query = "SELECT * FROM `click_payments` WHERE id = ?";

        // prepare the query to execute
        $statement = $this->conn->prepare($query);

        // execute the statement
        $statement->execute([$payment_id]);
        $statement->setFetchMode(\PDO::FETCH_ASSOC);

        // return response array-like
        return $statement->fetch();
    }

    /**
     * @name \App\Payment\Click\Models\Model::find_by_token, this method can find the payment data by token
     * @param $token string
     * @return array-like
     *
     * @example:
     *      $model = new Model();
     *      $payment = $model->find_by_token('aaaa-bbbb-cccc-dddddddd');
     */
    public function find_by_token($token)
    {
        // make sql query
        $query = "SELECT * FROM `click_payments` WHERE token = ?";

        // prepare the query to execute
        $statement = $this->conn->prepare($query);

        // execute the statement
        $statement->execute([$token]);
        $statement->setFetchMode(\PDO::FETCH_ASSOC);

        // return response array-like
        return $statement->fetch();
    }

    /**
     * @name \App\Payment\Click\Models\Model::find_by_merchant_trans_id, this method can find the payment data by merchant_trans_id
     * @param $merchant_trans_id integer
     * @return array-like
     *
     * @example:
     *      $model = new Model();
     *      $payment = $model->find_by_merchant_trans_id(2222);
     */
    public function find_by_merchant_trans_id($merchant_trans_id)
    {
        // make sql query
        $query = "SELECT * FROM `click_payments` WHERE merchant_trans_id = ?";

        // prepare the query to execute
        $statement = $this->conn->prepare($query);

        // execute the statement
        $statement->execute([$merchant_trans_id]);
        $statement->setFetchMode(\PDO::FETCH_ASSOC);

        // return response
        return $statement->fetch();
    }

    /**
     * @name \App\Payment\Click\Models\Model::update_by_id, this method can update the payment in databse by id
     * @param $payment_id integer
     * @param $arguments array-like
     * @return int
     *
     * @example:
     *      $model = new Model();
     *      $model->update_by_id(1111, [
     *          ...
     *      ]);
     */
    public function update_by_id($payment_id, $arguments = [])
    {
        // make sets
        $sets = $this->sets($arguments);
        $str = $sets['str'];
        $values = $sets['values'];

        // make query
        $query = "UPDATE `click_payments` SET $str WHERE id = :id";


        // prepare query to execute
        $statement = $this->conn->prepare($query);

        // execute the statement
        $values[':id'] = $payment_id;
        $statement->execute($values);

        // return response array-like
        return $statement->rowCount();
    }

    /**
     * @name \App\Payment\Click\Models\Model::update_by_token, this method can update the payment in databse by id
     * @param $token string
     * @param $arguments array-like
     * @return int
     *
     * @example:
     *      $model = new Model();
     *      $model->update_by_token('aaaa-bbbb-cccc-dddddddddd', [
     *          ...
     *      ]);
     */
    public function update_by_token($token, $arguments)
    {
        // make sets
        $sets = $this->sets($arguments);
        $str = $sets['str'];
        $values = $sets['values'];

        // make query
        $query = "UPDATE `click_payments` SET $str WHERE `token` = :token";

        // prepare query to execute
        $statement = $this->conn->prepare($query);

        // execute the statement
        $values[':token'] = $token;
        $statement->execute($values);

        // return response
        return $statement->rowCount();
    }

    /**
     * @name \App\Payment\Click\Models\Model::sets, this method makes query sets
     * @param $arguments array-like
     * @return array
     */
    private function sets($arguments)
    {
        $sets = [];
        $values = [];

        foreach ($arguments as $key => $value) {
            $sets[] = "$key = :$key";
            $values[":$key"] = $value;
        }

        // add the modified
        $sets[] = "modified = '" . date("Y-m-d H:i:s") . "'";

        // implode sets
        $result = [
            'str' => implode(', ', $sets),
            'values' => $values,
        ];

        // return response
        return $result;
    }
}
