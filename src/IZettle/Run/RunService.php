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

class RunService
{
    /**
     * @var EntityRepositoryInterface
     */
    private $runRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $logRepository;

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(
        EntityRepositoryInterface $runRepository,
        EntityRepositoryInterface $logRepository,
        Logger $logger
    ) {
        $this->runRepository = $runRepository;
        $this->logRepository = $logRepository;
        $this->logger = $logger;
    }

    public function startRun(string $salesChannelId, string $taskName, Context $context): string
    {
        $runId = Uuid::randomHex();

        $this->runRepository->create([[
            'id' => $runId,
            'salesChannelId' => $salesChannelId,
            'task' => $taskName,
        ]], $context);

        return $runId;
    }

    public function writeLog(string $runId, Context $context): void
    {
        $logHandler = $this->getLogHandler();
        if ($logHandler === null) {
            return;
        }
        $logs = $logHandler->getLogs();

        if (\count($logs) > 0) {
            foreach ($logs as &$log) {
                $log['runId'] = $runId;
            }
            unset($log);

            $this->logRepository->create($logs, $context);
        }

        $logHandler->flush();
    }

    public function finishRun(string $runId, Context $context): void
    {
        $this->runRepository->update([[
            'id' => $runId,
            'finishedAt' => new \DateTime(),
        ]], $context);
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
