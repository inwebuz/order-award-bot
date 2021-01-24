<?php

namespace App\Payment\Click\Models;

/**
 * @name PaymentsStatus class, the some std payment status
 *
 * @example
 *      if($payment['status'] == PaymentsStatus::CONFIRMED){
 *          ...
 *      }
 */
class PaymentsStatus
{
    /** @var string */
    const INPUT = 'input';

    /** @var string */
    const WAITING = 'waiting';

    /** @var string */
    const PREAUTH = 'preauth';

    /** @var string */
    const CONFIRMED = 'confirmed';

    /** @var string */
    const REJECTED = 'rejected';

    /** @var string */
    const REFUNDED = 'refunded';

    /** @var string */
    const ERROR = 'error';
}
