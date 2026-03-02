<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ConfigurableBundlePageSearch\Business\Expander;

use Generated\Shared\Transfer\ConfigurableBundleTemplatePageSearchTransfer;
use Generated\Shared\Transfer\ConfigurableBundleTemplateTransfer;
use Generated\Shared\Transfer\ProductImageSetTransfer;
use Spryker\Zed\ConfigurableBundlePageSearch\Dependency\Facade\ConfigurableBundlePageSearchToProductImageFacadeInterface;

class ConfigurableBundleTemplatePageSearchExpander implements ConfigurableBundleTemplatePageSearchExpanderInterface
{
    /**
     * @var \Spryker\Zed\ConfigurableBundlePageSearch\Dependency\Facade\ConfigurableBundlePageSearchToProductImageFacadeInterface
     */
    protected $productImageFacade;

    /**
     * @var array<\Spryker\Zed\ConfigurableBundlePageSearchExtension\Dependency\Plugin\ConfigurableBundleTemplatePageDataExpanderPluginInterface>
     */
    protected $configurableBundleTemplatePageDataExpanderPlugins;

    public function __construct(
        ConfigurableBundlePageSearchToProductImageFacadeInterface $productImageFacade,
        array $configurableBundleTemplatePageDataExpanderPlugins
    ) {
        $this->configurableBundleTemplatePageDataExpanderPlugins = $configurableBundleTemplatePageDataExpanderPlugins;
        $this->productImageFacade = $productImageFacade;
    }

    public function expand(
        ConfigurableBundleTemplateTransfer $configurableBundleTemplateTransfer,
        ConfigurableBundleTemplatePageSearchTransfer $configurableBundleTemplatePageSearchTransfer
    ): ConfigurableBundleTemplatePageSearchTransfer {
        $configurableBundleTemplatePageSearchTransfer = $this->expandConfigurableBundleTemplatePageSearchTransferWithImages(
            $configurableBundleTemplateTransfer,
            $configurableBundleTemplatePageSearchTransfer,
        );

        $configurableBundleTemplatePageSearchTransfer = $this->executeConfigurableBundleTemplatePageDataExpanderPlugins(
            $configurableBundleTemplateTransfer,
            $configurableBundleTemplatePageSearchTransfer,
        );

        return $configurableBundleTemplatePageSearchTransfer;
    }

    protected function executeConfigurableBundleTemplatePageDataExpanderPlugins(
        ConfigurableBundleTemplateTransfer $configurableBundleTemplateTransfer,
        ConfigurableBundleTemplatePageSearchTransfer $configurableBundleTemplatePageSearchTransfer
    ): ConfigurableBundleTemplatePageSearchTransfer {
        foreach ($this->configurableBundleTemplatePageDataExpanderPlugins as $configurableBundleTemplatePageDataExpanderPlugin) {
            $configurableBundleTemplatePageSearchTransfer = $configurableBundleTemplatePageDataExpanderPlugin->expand(
                $configurableBundleTemplateTransfer,
                $configurableBundleTemplatePageSearchTransfer,
            );
        }

        return $configurableBundleTemplatePageSearchTransfer;
    }

    protected function expandConfigurableBundleTemplatePageSearchTransferWithImages(
        ConfigurableBundleTemplateTransfer $configurableBundleTemplateTransfer,
        ConfigurableBundleTemplatePageSearchTransfer $configurableBundleTemplatePageSearchTransfer
    ): ConfigurableBundleTemplatePageSearchTransfer {
        $images = [];

        $productImageSetTransfers = $this->productImageFacade->resolveProductImageSetsForLocale(
            $configurableBundleTemplateTransfer->getProductImageSets(),
            $configurableBundleTemplatePageSearchTransfer->getLocale(),
        );

        foreach ($productImageSetTransfers as $productImageSetTransfer) {
            $images = array_merge($images, $this->getImagesArrayFromImageSetTransfer($productImageSetTransfer));
        }

        $configurableBundleTemplatePageSearchTransfer->setImages($images);

        return $configurableBundleTemplatePageSearchTransfer;
    }

    protected function getImagesArrayFromImageSetTransfer(ProductImageSetTransfer $productImageSetTransfer): array
    {
        $images = [];

        foreach ($productImageSetTransfer->getProductImages() as $productImageTransfer) {
            $images[] = $productImageTransfer->toArray(false, true);
        }

        return $images;
    }
}
