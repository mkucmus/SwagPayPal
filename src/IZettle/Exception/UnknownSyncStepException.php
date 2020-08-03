<?php declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swag\PayPal\IZettle\Exception;

use Shopware\Core\Framework\ShopwareHttpException;

class UnknownSyncStepException extends ShopwareHttpException
{
    public function __construct(string $syncStep)
    {
        parent::__construct(
            'The sync step "{{ syncStep }}" is not recognized.',
            ['syncStep' => $syncStep]
        );
    }

    public function getErrorCode(): string
    {
        return 'SWAG_PAYPAL_IZETTLE__UNKNOWN_SYNC_STEP';
    }
}
