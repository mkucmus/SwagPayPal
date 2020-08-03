<?php declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swag\PayPal\IZettle\Sync;

use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Swag\PayPal\IZettle\Api\Service\ProductConverter;
use Swag\PayPal\IZettle\Sync\Context\ProductContextFactory;
use Swag\PayPal\IZettle\Sync\Product\DeletedUpdater;
use Swag\PayPal\IZettle\Sync\Product\NewUpdater;
use Swag\PayPal\IZettle\Sync\Product\OutdatedUpdater;
use Swag\PayPal\IZettle\Sync\Product\UnsyncedChecker;

class ProductSyncer extends AbstractSyncer
{
    /**
     * @var ProductConverter
     */
    private $productConverter;

    /**
     * @var ProductContextFactory
     */
    private $productContextFactory;

    /**
     * @var NewUpdater
     */
    private $newUpdater;

    /**
     * @var OutdatedUpdater
     */
    private $outdatedUpdater;

    /**
     * @var DeletedUpdater
     */
    private $deletedUpdater;

    /**
     * @var UnsyncedChecker
     */
    private $unsyncedChecker;

    public function __construct(
        ProductConverter $productConverter,
        ProductContextFactory $productContextFactory,
        NewUpdater $newUpdater,
        OutdatedUpdater $outdatedUpdater,
        DeletedUpdater $deletedUpdater,
        UnsyncedChecker $unsyncedChecker
    ) {
        $this->productConverter = $productConverter;
        $this->productContextFactory = $productContextFactory;
        $this->newUpdater = $newUpdater;
        $this->outdatedUpdater = $outdatedUpdater;
        $this->deletedUpdater = $deletedUpdater;
        $this->unsyncedChecker = $unsyncedChecker;
    }

    /**
     * @param ProductCollection $entityCollection
     */
    public function sync(
        EntityCollection $entityCollection,
        SalesChannelEntity $salesChannel,
        Context $context
    ): void {
        $productContext = $this->productContextFactory->getContext($salesChannel, $context);
        $currency = $productContext->getIZettleSalesChannel()->isSyncPrices() ? $salesChannel->getCurrency() : null;

        $productGroupings = $this->productConverter->convertShopwareProducts($entityCollection, $currency, $productContext);

        $this->newUpdater->update($productGroupings, $productContext);
        $this->productContextFactory->commit($productContext);

        $this->outdatedUpdater->update($productGroupings, $productContext);
        $this->productContextFactory->commit($productContext);
    }

    /**
     * @param string[] $productIds
     */
    public function cleanUp(
        array $productIds,
        SalesChannelEntity $salesChannel,
        Context $context
    ): void {
        $productContext = $this->productContextFactory->getContext($salesChannel, $context);

        $this->unsyncedChecker->checkForUnsynced($productIds, $productContext);
        $this->deletedUpdater->update($productIds, $productContext);
        $this->productContextFactory->commit($productContext);
    }
}
