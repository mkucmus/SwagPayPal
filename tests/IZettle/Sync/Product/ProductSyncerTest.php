<?php declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swag\PayPal\Test\IZettle\Sync\Product;

use PHPUnit\Framework\MockObject\MockObject;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Framework\Context;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Swag\PayPal\IZettle\Api\Service\Converter\CategoryConverter;
use Swag\PayPal\IZettle\Api\Service\Converter\OptionGroupConverter;
use Swag\PayPal\IZettle\Api\Service\Converter\PresentationConverter;
use Swag\PayPal\IZettle\Api\Service\Converter\PriceConverter;
use Swag\PayPal\IZettle\Api\Service\Converter\UuidConverter;
use Swag\PayPal\IZettle\Api\Service\Converter\VariantConverter;
use Swag\PayPal\IZettle\Api\Service\ProductConverter;
use Swag\PayPal\IZettle\Resource\ProductResource;
use Swag\PayPal\IZettle\Sync\Context\ProductContextFactory;
use Swag\PayPal\IZettle\Sync\Product\DeletedUpdater;
use Swag\PayPal\IZettle\Sync\Product\NewUpdater;
use Swag\PayPal\IZettle\Sync\Product\OutdatedUpdater;
use Swag\PayPal\IZettle\Sync\Product\UnsyncedChecker;
use Swag\PayPal\IZettle\Sync\ProductSyncer;
use Swag\PayPal\Test\IZettle\Mock\ProductContextMock;

class ProductSyncerTest extends AbstractProductSyncTest
{
    /**
     * @var MockObject
     */
    private $productContextFactory;

    /**
     * @var ProductContextMock
     */
    private $productContext;

    /**
     * @var ProductSyncer
     */
    private $pruductSyncer;

    /**
     * @var MockObject
     */
    private $productResource;

    /**
     * @var SalesChannelEntity
     */
    private $salesChannel;

    /**
     * @var MockObject
     */
    private $newUpdater;

    /**
     * @var MockObject
     */
    private $outdatedUpdater;

    /**
     * @var MockObject
     */
    private $deletedUpdater;

    /**
     * @var MockObject
     */
    private $unsyncedChecker;

    public function setUp(): void
    {
        $context = Context::createDefaultContext();

        $this->salesChannel = $this->getSalesChannel($context);

        $this->productContext = new ProductContextMock($this->salesChannel, $context);
        $this->productContextFactory = $this->createMock(ProductContextFactory::class);
        $this->productContextFactory->method('getContext')->willReturn($this->productContext);

        $this->productResource = $this->createPartialMock(
            ProductResource::class,
            ['createProduct', 'updateProduct', 'deleteProduct']
        );

        $this->newUpdater = $this->createMock(NewUpdater::class);
        $this->outdatedUpdater = $this->createMock(OutdatedUpdater::class);
        $this->deletedUpdater = $this->createMock(DeletedUpdater::class);
        $this->unsyncedChecker = $this->createMock(UnsyncedChecker::class);

        $this->pruductSyncer = new ProductSyncer(
            new ProductConverter(
                new UuidConverter(),
                new CategoryConverter(new UuidConverter()),
                new VariantConverter(new UuidConverter(), new PriceConverter(), new PresentationConverter()),
                new OptionGroupConverter(),
                new PresentationConverter()
            ),
            $this->productContextFactory,
            $this->newUpdater,
            $this->outdatedUpdater,
            $this->deletedUpdater,
            $this->unsyncedChecker
        );
    }

    public function testProductSync(): void
    {
        $context = Context::createDefaultContext();

        $product = $this->getProduct();

        $this->productResource->expects(static::never())->method('updateProduct');
        $this->productResource->expects(static::never())->method('createProduct');
        $this->productContextFactory->expects(static::exactly(2))->method('commit');
        $this->newUpdater->expects(static::once())->method('update');
        $this->outdatedUpdater->expects(static::once())->method('update');

        $this->pruductSyncer->sync(new ProductCollection([$product]), $this->salesChannel, $context);

        static::assertCount(0, $this->productContext->getProductChanges());
        static::assertCount(0, $this->productContext->getProductRemovals());
    }

    public function testCleanUp(): void
    {
        $context = Context::createDefaultContext();

        $product = $this->getProduct();

        $this->productResource->expects(static::never())->method('updateProduct');
        $this->productResource->expects(static::never())->method('createProduct');
        $this->productContextFactory->expects(static::once())->method('commit');
        $this->deletedUpdater->expects(static::once())->method('update');
        $this->unsyncedChecker->expects(static::once())->method('checkForUnsynced');

        $this->pruductSyncer->cleanUp([$product->getId()], $this->salesChannel, $context);

        static::assertCount(0, $this->productContext->getProductChanges());
        static::assertCount(0, $this->productContext->getProductRemovals());
    }
}
