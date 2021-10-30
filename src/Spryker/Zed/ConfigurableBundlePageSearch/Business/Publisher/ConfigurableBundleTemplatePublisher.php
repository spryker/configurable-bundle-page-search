<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ConfigurableBundlePageSearch\Business\Publisher;

use Generated\Shared\Transfer\ConfigurableBundleTemplateFilterTransfer;
use Generated\Shared\Transfer\ConfigurableBundleTemplatePageSearchCollectionTransfer;
use Generated\Shared\Transfer\ConfigurableBundleTemplatePageSearchFilterTransfer;
use Generated\Shared\Transfer\ConfigurableBundleTemplatePageSearchTransfer;
use Generated\Shared\Transfer\ConfigurableBundleTemplateTransfer;
use Spryker\Zed\ConfigurableBundlePageSearch\Business\Expander\ConfigurableBundleTemplatePageSearchExpanderInterface;
use Spryker\Zed\ConfigurableBundlePageSearch\Business\Mapper\ConfigurableBundleTemplatePageSearchMapperInterface;
use Spryker\Zed\ConfigurableBundlePageSearch\Dependency\Facade\ConfigurableBundlePageSearchToConfigurableBundleFacadeInterface;
use Spryker\Zed\ConfigurableBundlePageSearch\Persistence\ConfigurableBundlePageSearchEntityManagerInterface;
use Spryker\Zed\ConfigurableBundlePageSearch\Persistence\ConfigurableBundlePageSearchRepositoryInterface;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;

class ConfigurableBundleTemplatePublisher implements ConfigurableBundleTemplatePublisherInterface
{
    use TransactionTrait;

    /**
     * @var \Spryker\Zed\ConfigurableBundlePageSearch\Dependency\Facade\ConfigurableBundlePageSearchToConfigurableBundleFacadeInterface
     */
    protected $configurableBundleFacade;

    /**
     * @var \Spryker\Zed\ConfigurableBundlePageSearch\Persistence\ConfigurableBundlePageSearchRepositoryInterface
     */
    protected $configurableBundlePageSearchRepository;

    /**
     * @var \Spryker\Zed\ConfigurableBundlePageSearch\Persistence\ConfigurableBundlePageSearchEntityManagerInterface
     */
    protected $configurableBundlePageSearchEntityManager;

    /**
     * @var \Spryker\Zed\ConfigurableBundlePageSearch\Business\Mapper\ConfigurableBundleTemplatePageSearchMapperInterface
     */
    protected $configurableBundleTemplatePageSearchMapper;

    /**
     * @var \Spryker\Zed\ConfigurableBundlePageSearch\Business\Expander\ConfigurableBundleTemplatePageSearchExpanderInterface
     */
    protected $configurableBundleTemplatePageSearchExpander;

    /**
     * @param \Spryker\Zed\ConfigurableBundlePageSearch\Dependency\Facade\ConfigurableBundlePageSearchToConfigurableBundleFacadeInterface $configurableBundleFacade
     * @param \Spryker\Zed\ConfigurableBundlePageSearch\Persistence\ConfigurableBundlePageSearchRepositoryInterface $configurableBundlePageSearchRepository
     * @param \Spryker\Zed\ConfigurableBundlePageSearch\Persistence\ConfigurableBundlePageSearchEntityManagerInterface $configurableBundlePageSearchEntityManager
     * @param \Spryker\Zed\ConfigurableBundlePageSearch\Business\Mapper\ConfigurableBundleTemplatePageSearchMapperInterface $configurableBundleTemplatePageSearchMapper
     * @param \Spryker\Zed\ConfigurableBundlePageSearch\Business\Expander\ConfigurableBundleTemplatePageSearchExpanderInterface $configurableBundleTemplatePageSearchExpander
     */
    public function __construct(
        ConfigurableBundlePageSearchToConfigurableBundleFacadeInterface $configurableBundleFacade,
        ConfigurableBundlePageSearchRepositoryInterface $configurableBundlePageSearchRepository,
        ConfigurableBundlePageSearchEntityManagerInterface $configurableBundlePageSearchEntityManager,
        ConfigurableBundleTemplatePageSearchMapperInterface $configurableBundleTemplatePageSearchMapper,
        ConfigurableBundleTemplatePageSearchExpanderInterface $configurableBundleTemplatePageSearchExpander
    ) {
        $this->configurableBundleFacade = $configurableBundleFacade;
        $this->configurableBundlePageSearchRepository = $configurableBundlePageSearchRepository;
        $this->configurableBundlePageSearchEntityManager = $configurableBundlePageSearchEntityManager;
        $this->configurableBundleTemplatePageSearchMapper = $configurableBundleTemplatePageSearchMapper;
        $this->configurableBundleTemplatePageSearchExpander = $configurableBundleTemplatePageSearchExpander;
    }

    /**
     * @param array<int> $configurableBundleTemplateIds
     *
     * @return void
     */
    public function publish(array $configurableBundleTemplateIds): void
    {
        $configurableBundleTemplateIds = array_unique(array_filter($configurableBundleTemplateIds));

        if (!$configurableBundleTemplateIds) {
            return;
        }

        $configurableBundleTemplateTransfers = $this->configurableBundleFacade->getConfigurableBundleTemplateCollection(
            (new ConfigurableBundleTemplateFilterTransfer())->setConfigurableBundleTemplateIds($configurableBundleTemplateIds),
        );
        $configurableBundleTemplatePageSearchTransfers = $this->getConfigurableBundleTemplatePageSearchTransfers($configurableBundleTemplateIds);

        $this->getTransactionHandler()->handleTransaction(function () use ($configurableBundleTemplateTransfers, $configurableBundleTemplatePageSearchTransfers): void {
            $this->executePublishTransaction($configurableBundleTemplateTransfers->getConfigurableBundleTemplates()->getArrayCopy(), $configurableBundleTemplatePageSearchTransfers);
        });
    }

    /**
     * @param array<\Generated\Shared\Transfer\ConfigurableBundleTemplateTransfer> $configurableBundleTemplateTransfers
     * @param array<array<\Generated\Shared\Transfer\ConfigurableBundleTemplatePageSearchTransfer>> $groupedConfigurableBundleTemplatePageSearchTransfers
     *
     * @return void
     */
    protected function executePublishTransaction(array $configurableBundleTemplateTransfers, array $groupedConfigurableBundleTemplatePageSearchTransfers): void
    {
        foreach ($configurableBundleTemplateTransfers as $configurableBundleTemplateTransfer) {
            $configurableBundleTemplatePageSearchTransfers = $groupedConfigurableBundleTemplatePageSearchTransfers[$configurableBundleTemplateTransfer->getIdConfigurableBundleTemplate()] ?? [];

            if (!$configurableBundleTemplateTransfer->getIsActive()) {
                if ($configurableBundleTemplatePageSearchTransfers) {
                    $this->deleteConfigurableBundleTemplatePageSearches($configurableBundleTemplatePageSearchTransfers);
                }

                continue;
            }

            $this->storeConfigurableBundlePageSearches(
                $configurableBundleTemplateTransfer,
                $configurableBundleTemplatePageSearchTransfers,
            );
        }
    }

    /**
     * @param array<int> $configurableBundleTemplateIds
     *
     * @return array<array<\Generated\Shared\Transfer\ConfigurableBundleTemplatePageSearchTransfer>>
     */
    protected function getConfigurableBundleTemplatePageSearchTransfers(array $configurableBundleTemplateIds): array
    {
        $configurableBundleTemplatePageSearchCollectionTransfer = $this->configurableBundlePageSearchRepository->getConfigurableBundleTemplatePageSearchCollection(
            (new ConfigurableBundleTemplatePageSearchFilterTransfer())->setConfigurableBundleTemplateIds($configurableBundleTemplateIds),
        );

        return $this->groupConfigurableBundleTemplatePageSearchTransfers($configurableBundleTemplatePageSearchCollectionTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\ConfigurableBundleTemplatePageSearchCollectionTransfer $configurableBundleTemplatePageSearchCollectionTransfer
     *
     * @return array<array<\Generated\Shared\Transfer\ConfigurableBundleTemplatePageSearchTransfer>>
     */
    protected function groupConfigurableBundleTemplatePageSearchTransfers(
        ConfigurableBundleTemplatePageSearchCollectionTransfer $configurableBundleTemplatePageSearchCollectionTransfer
    ): array {
        $groupedConfigurableBundleTemplatePageSearchTransfers = [];

        foreach ($configurableBundleTemplatePageSearchCollectionTransfer->getConfigurableBundleTemplatePageSearches() as $configurableBundleTemplatePageSearchTransfer) {
            $fkConfigurableBundleTemplate = $configurableBundleTemplatePageSearchTransfer->getFkConfigurableBundleTemplate();
            $locale = $configurableBundleTemplatePageSearchTransfer->getLocale();

            $groupedConfigurableBundleTemplatePageSearchTransfers[$fkConfigurableBundleTemplate][$locale] = $configurableBundleTemplatePageSearchTransfer;
        }

        return $groupedConfigurableBundleTemplatePageSearchTransfers;
    }

    /**
     * @param \Generated\Shared\Transfer\ConfigurableBundleTemplateTransfer $configurableBundleTemplateTransfer
     * @param array<\Generated\Shared\Transfer\ConfigurableBundleTemplatePageSearchTransfer> $configurableBundleTemplatePageSearchTransfers
     *
     * @return void
     */
    protected function storeConfigurableBundlePageSearches(
        ConfigurableBundleTemplateTransfer $configurableBundleTemplateTransfer,
        array $configurableBundleTemplatePageSearchTransfers
    ): void {
        $configurableBundleTemplatePageSearchTransfers = $this->getMappedConfigurableBundleTemplatePageSearchTransfers(
            $configurableBundleTemplateTransfer,
            $configurableBundleTemplatePageSearchTransfers,
        );

        foreach ($configurableBundleTemplatePageSearchTransfers as $configurableBundleTemplatePageSearchTransfer) {
            $this->storeSingleConfigurableBundlePageSearch($configurableBundleTemplatePageSearchTransfer);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\ConfigurableBundleTemplatePageSearchTransfer $configurableBundleTemplatePageSearchTransfer
     *
     * @return void
     */
    protected function storeSingleConfigurableBundlePageSearch(ConfigurableBundleTemplatePageSearchTransfer $configurableBundleTemplatePageSearchTransfer): void
    {
        if (!$configurableBundleTemplatePageSearchTransfer->getIdConfigurableBundleTemplatePageSearch()) {
            $this->configurableBundlePageSearchEntityManager->createConfigurableBundlePageSearch($configurableBundleTemplatePageSearchTransfer);

            return;
        }

        $this->configurableBundlePageSearchEntityManager->updateConfigurableBundlePageSearch($configurableBundleTemplatePageSearchTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\ConfigurableBundleTemplateTransfer $configurableBundleTemplateTransfer
     * @param array<\Generated\Shared\Transfer\ConfigurableBundleTemplatePageSearchTransfer> $configurableBundleTemplatePageSearchTransfers
     *
     * @return array<\Generated\Shared\Transfer\ConfigurableBundleTemplatePageSearchTransfer>
     */
    protected function getMappedConfigurableBundleTemplatePageSearchTransfers(
        ConfigurableBundleTemplateTransfer $configurableBundleTemplateTransfer,
        array $configurableBundleTemplatePageSearchTransfers
    ): array {
        $mappedConfigurableBundleTemplatePageSearchTransfers = [];

        foreach ($configurableBundleTemplateTransfer->getTranslations() as $configurableBundleTemplateTranslationTransfer) {
            $configurableBundleTemplateTranslationTransfer
                ->requireLocale()
                ->getLocale()
                    ->requireLocaleName();

            $localeTransfer = $configurableBundleTemplateTranslationTransfer->getLocale();

            $mappedConfigurableBundleTemplatePageSearchTransfers[] = $this->configurableBundleTemplatePageSearchMapper->mapConfigurableBundleTemplateTransferToPageSearchTransfer(
                $configurableBundleTemplateTransfer,
                $localeTransfer,
                $configurableBundleTemplatePageSearchTransfers[$localeTransfer->getLocaleName()] ?? new ConfigurableBundleTemplatePageSearchTransfer(),
            );
        }

        return $mappedConfigurableBundleTemplatePageSearchTransfers;
    }

    /**
     * @param array<\Generated\Shared\Transfer\ConfigurableBundleTemplatePageSearchTransfer> $configurableBundleTemplatePageSearchTransfers
     *
     * @return void
     */
    protected function deleteConfigurableBundleTemplatePageSearches(array $configurableBundleTemplatePageSearchTransfers): void
    {
        foreach ($configurableBundleTemplatePageSearchTransfers as $configurableBundleTemplatePageSearchTransfer) {
            $this->configurableBundlePageSearchEntityManager->deleteConfigurableBundlePageSearch($configurableBundleTemplatePageSearchTransfer);
        }
    }
}
