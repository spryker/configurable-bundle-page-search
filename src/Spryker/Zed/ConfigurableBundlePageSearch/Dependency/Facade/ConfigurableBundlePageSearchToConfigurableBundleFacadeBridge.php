<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ConfigurableBundlePageSearch\Dependency\Facade;

use Generated\Shared\Transfer\ConfigurableBundleTemplateCollectionTransfer;
use Generated\Shared\Transfer\ConfigurableBundleTemplateFilterTransfer;

class ConfigurableBundlePageSearchToConfigurableBundleFacadeBridge implements ConfigurableBundlePageSearchToConfigurableBundleFacadeInterface
{
    /**
     * @var \Spryker\Zed\ConfigurableBundle\Business\ConfigurableBundleFacadeInterface
     */
    protected $configurableBundleFacade;

    /**
     * @param \Spryker\Zed\ConfigurableBundle\Business\ConfigurableBundleFacadeInterface $configurableBundleFacade
     */
    public function __construct($configurableBundleFacade)
    {
        $this->configurableBundleFacade = $configurableBundleFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\ConfigurableBundleTemplateFilterTransfer $configurableBundleTemplateFilterTransfer
     *
     * @return \Generated\Shared\Transfer\ConfigurableBundleTemplateCollectionTransfer
     */
    public function getConfigurableBundleTemplateCollection(
        ConfigurableBundleTemplateFilterTransfer $configurableBundleTemplateFilterTransfer
    ): ConfigurableBundleTemplateCollectionTransfer {
        return $this->configurableBundleFacade->getConfigurableBundleTemplateCollection($configurableBundleTemplateFilterTransfer);
    }
}
