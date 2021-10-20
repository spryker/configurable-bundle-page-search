<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ConfigurableBundlePageSearch\Business\Mapper;

use Generated\Shared\Transfer\ConfigurableBundleTemplatePageSearchTransfer;
use Generated\Shared\Transfer\ConfigurableBundleTemplateTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Spryker\Shared\ConfigurableBundlePageSearch\ConfigurableBundlePageSearchConfig;
use Spryker\Zed\ConfigurableBundlePageSearch\Business\Expander\ConfigurableBundleTemplatePageSearchExpanderInterface;
use Spryker\Zed\ConfigurableBundlePageSearch\Business\Search\DataMapper\ConfigurableBundlePageSearchDataMapperInterface;
use Spryker\Zed\ConfigurableBundlePageSearch\Dependency\Service\ConfigurableBundlePageSearchToUtilEncodingServiceInterface;

class ConfigurableBundleTemplatePageSearchMapper implements ConfigurableBundleTemplatePageSearchMapperInterface
{
    /**
     * @var \Spryker\Zed\ConfigurableBundlePageSearch\Dependency\Service\ConfigurableBundlePageSearchToUtilEncodingServiceInterface
     */
    protected $utilEncodingService;

    /**
     * @var \Spryker\Zed\ConfigurableBundlePageSearch\Business\Search\DataMapper\ConfigurableBundlePageSearchDataMapperInterface
     */
    protected $configurableBundlePageSearchDataMapper;

    /**
     * @var \Spryker\Zed\ConfigurableBundlePageSearch\Business\Expander\ConfigurableBundleTemplatePageSearchExpanderInterface
     */
    protected $configurableBundleTemplatePageSearchExpander;

    /**
     * @param \Spryker\Zed\ConfigurableBundlePageSearch\Dependency\Service\ConfigurableBundlePageSearchToUtilEncodingServiceInterface $utilEncodingService
     * @param \Spryker\Zed\ConfigurableBundlePageSearch\Business\Search\DataMapper\ConfigurableBundlePageSearchDataMapperInterface $configurableBundlePageSearchDataMapper
     * @param \Spryker\Zed\ConfigurableBundlePageSearch\Business\Expander\ConfigurableBundleTemplatePageSearchExpanderInterface $configurableBundleTemplatePageSearchExpander
     */
    public function __construct(
        ConfigurableBundlePageSearchToUtilEncodingServiceInterface $utilEncodingService,
        ConfigurableBundlePageSearchDataMapperInterface $configurableBundlePageSearchDataMapper,
        ConfigurableBundleTemplatePageSearchExpanderInterface $configurableBundleTemplatePageSearchExpander
    ) {
        $this->utilEncodingService = $utilEncodingService;
        $this->configurableBundlePageSearchDataMapper = $configurableBundlePageSearchDataMapper;
        $this->configurableBundleTemplatePageSearchExpander = $configurableBundleTemplatePageSearchExpander;
    }

    /**
     * @param \Generated\Shared\Transfer\ConfigurableBundleTemplateTransfer $configurableBundleTemplateTransfer
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     * @param \Generated\Shared\Transfer\ConfigurableBundleTemplatePageSearchTransfer $configurableBundleTemplatePageSearchTransfer
     *
     * @return \Generated\Shared\Transfer\ConfigurableBundleTemplatePageSearchTransfer
     */
    public function mapConfigurableBundleTemplateTransferToPageSearchTransfer(
        ConfigurableBundleTemplateTransfer $configurableBundleTemplateTransfer,
        LocaleTransfer $localeTransfer,
        ConfigurableBundleTemplatePageSearchTransfer $configurableBundleTemplatePageSearchTransfer
    ): ConfigurableBundleTemplatePageSearchTransfer {
        $configurableBundleTemplatePageSearchTransfer = $this->mapConfigurableBundleTemplateTransferToConfigurableBundleTemplatePageSearchTransfer(
            $configurableBundleTemplateTransfer,
            $configurableBundleTemplatePageSearchTransfer,
        );

        $configurableBundleTemplatePageSearchTransfer->setLocale($localeTransfer->getLocaleName());

        $configurableBundleTemplatePageSearchTransfer = $this->configurableBundleTemplatePageSearchExpander->expand(
            $configurableBundleTemplateTransfer,
            $configurableBundleTemplatePageSearchTransfer,
        );

        $configurableBundleTemplatePageSearchTransfer->setData(
            $this->getConfigurableBundleTemplatePageSearchTransferData($configurableBundleTemplatePageSearchTransfer, $localeTransfer),
        );

        $configurableBundleTemplatePageSearchTransfer->setStructuredData(
            $this->getConfigurableBundleTemplatePageSearchTransferStructuredData($configurableBundleTemplatePageSearchTransfer),
        );

        return $configurableBundleTemplatePageSearchTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ConfigurableBundleTemplateTransfer $configurableBundleTemplateTransfer
     * @param \Generated\Shared\Transfer\ConfigurableBundleTemplatePageSearchTransfer $configurableBundleTemplatePageSearchTransfer
     *
     * @return \Generated\Shared\Transfer\ConfigurableBundleTemplatePageSearchTransfer
     */
    protected function mapConfigurableBundleTemplateTransferToConfigurableBundleTemplatePageSearchTransfer(
        ConfigurableBundleTemplateTransfer $configurableBundleTemplateTransfer,
        ConfigurableBundleTemplatePageSearchTransfer $configurableBundleTemplatePageSearchTransfer
    ): ConfigurableBundleTemplatePageSearchTransfer {
        $configurableBundleTemplatePageSearchTransfer = $configurableBundleTemplatePageSearchTransfer->fromArray(
            $configurableBundleTemplateTransfer->toArray(),
            true,
        );

        return $configurableBundleTemplatePageSearchTransfer->setType(ConfigurableBundlePageSearchConfig::CONFIGURABLE_BUNDLE_TEMPLATE_RESOURCE_NAME)
            ->setFkConfigurableBundleTemplate(
                $configurableBundleTemplateTransfer->getIdConfigurableBundleTemplate(),
            );
    }

    /**
     * @param \Generated\Shared\Transfer\ConfigurableBundleTemplatePageSearchTransfer $configurableBundleTemplatePageSearchTransfer
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     *
     * @return string|null
     */
    protected function getConfigurableBundleTemplatePageSearchTransferData(
        ConfigurableBundleTemplatePageSearchTransfer $configurableBundleTemplatePageSearchTransfer,
        LocaleTransfer $localeTransfer
    ): ?string {
        $data = $this->configurableBundlePageSearchDataMapper->mapConfigurableBundleTemplatePageSearchTransferToSearchData(
            $configurableBundleTemplatePageSearchTransfer,
            $localeTransfer,
        );

        return $this->utilEncodingService->encodeJson($data);
    }

    /**
     * @param \Generated\Shared\Transfer\ConfigurableBundleTemplatePageSearchTransfer $configurableBundleTemplatePageSearchTransfer
     *
     * @return string|null
     */
    protected function getConfigurableBundleTemplatePageSearchTransferStructuredData(
        ConfigurableBundleTemplatePageSearchTransfer $configurableBundleTemplatePageSearchTransfer
    ): ?string {
        $structuredData = $configurableBundleTemplatePageSearchTransfer->toArray(true, true);

        unset($structuredData[ConfigurableBundleTemplatePageSearchTransfer::ID_CONFIGURABLE_BUNDLE_TEMPLATE_PAGE_SEARCH]);
        unset($structuredData[ConfigurableBundleTemplatePageSearchTransfer::STRUCTURED_DATA]);

        return $this->utilEncodingService->encodeJson($structuredData);
    }
}
