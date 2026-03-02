<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ConfigurableBundlePageSearch\Persistence;

use Generated\Shared\Transfer\ConfigurableBundleTemplateCollectionTransfer;
use Generated\Shared\Transfer\ConfigurableBundleTemplatePageSearchCollectionTransfer;
use Generated\Shared\Transfer\ConfigurableBundleTemplatePageSearchFilterTransfer;
use Generated\Shared\Transfer\FilterTransfer;

interface ConfigurableBundlePageSearchRepositoryInterface
{
    public function getConfigurableBundleTemplatePageSearchCollection(
        ConfigurableBundleTemplatePageSearchFilterTransfer $configurableBundleTemplatePageSearchFilterTransfer
    ): ConfigurableBundleTemplatePageSearchCollectionTransfer;

    public function getConfigurableBundleTemplateCollection(FilterTransfer $filterTransfer): ConfigurableBundleTemplateCollectionTransfer;
}
