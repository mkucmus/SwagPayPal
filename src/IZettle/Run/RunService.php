<?php declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swag\PayPal\IZettle\Run;

use Monolog\Logger;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\Uuid\Uuid;
use Swag\PayPal\IZettle\DataAbstractionLayer\Entity\IZettleSalesChannelRunEntity;

class RunService
{
    /**
     * @var EntityRepositoryInterface
     */
    private $runRepository;

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(
        EntityRepositoryInterface $runRepository,
        Logger $logger
    ) {
        $this->runRepository = $runRepository;
        $this->logger = $logger;
    }

    public function startRun(string $salesChannelId, string $taskName, Context $context): IZettleSalesChannelRunEntity
    {
        $run = new IZettleSalesChannelRunEntity();
        $run->setId(Uuid::randomHex());
        $run->setSalesChannelId($salesChannelId);
        $run->setTask($taskName);

        $this->runRepository->create([[
            'id' => $run->getId(),
            'salesChannelId' => $salesChannelId,
            'task' => $taskName,
        ]], $context);

        return $run;
    }

    public function finishRun(IZettleSalesChannelRunEntity $run, Context $context): void
    {
        $logHandler = $this->getLogHandler();
        if ($logHandler === null) {
            return;
        }
        $logs = $logHandler->getLogs();

        $this->runRepository->update([[
            'id' => $run->getId(),
            'logs' => $logs,
        ]], $context);

        $logHandler->flush();
    }

    private function getLogHandler(): ?LogHandler
    {
        foreach ($this->logger->getHandlers() as $handler) {
            if ($handler instanceof LogHandler) {
                return $handler;
            }
        }

        return null;
    }
}
