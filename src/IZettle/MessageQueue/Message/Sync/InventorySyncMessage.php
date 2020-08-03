<?php declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swag\PayPal\IZettle\MessageQueue\Message\Sync;

use Swag\PayPal\IZettle\MessageQueue\Message\AbstractSyncMessage;
use Swag\PayPal\IZettle\Sync\Context\InventoryContext;

class InventorySyncMessage extends AbstractSyncMessage
{
    /**
     * @var InventoryContext
     */
    private $inventoryContext;

    public function getInventoryContext(): InventoryContext
    {
        return $this->inventoryContext;
    }

    public function setInventoryContext(InventoryContext $inventoryContext): void
    {
        $this->inventoryContext = $inventoryContext;
    }
}
