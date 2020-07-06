<?php declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swag\PayPal\Test\IZettle\Mock\Client\_fixtures;

use Swag\PayPal\Test\IZettle\ConstantsForTesting;

class GetInventoryFixture
{
    public static function get(): array
    {
        return [
            'locationUuid' => ConstantsForTesting::LOCATION_STORE,
            'trackedProducts' => [
                ConstantsForTesting::PRODUCT_A_ID_CONVERTED,
                ConstantsForTesting::PRODUCT_C_ID_CONVERTED,
                ConstantsForTesting::PRODUCT_D_ID_CONVERTED,
                ConstantsForTesting::PRODUCT_E_ID_CONVERTED,
                '20fd7ad9-6b04-1237-9135-fe281c9c47d8',
            ],
            'variants' => [
                [
                    'locationUuid' => ConstantsForTesting::LOCATION_STORE,
                    'locationType' => 'STORE',
                    'productUuid' => ConstantsForTesting::PRODUCT_A_ID_CONVERTED,
                    'variantUuid' => ConstantsForTesting::PRODUCT_A_ID_VARIANT,
                    'balance' => '1',
                ],
                [
                    'locationUuid' => ConstantsForTesting::LOCATION_STORE,
                    'locationType' => 'STORE',
                    'productUuid' => ConstantsForTesting::PRODUCT_C_ID_CONVERTED,
                    'variantUuid' => ConstantsForTesting::PRODUCT_C_ID_VARIANT,
                    'balance' => '0',
                ],
                [
                    'locationUuid' => ConstantsForTesting::LOCATION_STORE,
                    'locationType' => 'STORE',
                    'productUuid' => ConstantsForTesting::PRODUCT_D_ID_CONVERTED,
                    'variantUuid' => ConstantsForTesting::PRODUCT_D_ID_VARIANT,
                    'balance' => '3',
                ],
                [
                    'locationUuid' => ConstantsForTesting::LOCATION_STORE,
                    'locationType' => 'STORE',
                    'productUuid' => ConstantsForTesting::PRODUCT_E_ID_CONVERTED,
                    'variantUuid' => ConstantsForTesting::PRODUCT_E_ID_VARIANT,
                    'balance' => '3',
                ],
                [
                    'locationUuid' => ConstantsForTesting::LOCATION_STORE,
                    'locationType' => 'STORE',
                    'productUuid' => '20fd7ad9-6b04-1237-9135-fe281c9c47d8',
                    'variantUuid' => '20fd7ad9-6b04-1237-9135-fe281c9c47d9',
                    'balance' => '55',
                ],
            ],
        ];
    }
}
