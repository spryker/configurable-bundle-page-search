<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ConfigurableBundlePageSearch\Persistence;

use Generated\Shared\Transfer\ConfigurableBundleTemplatePageSearchTransfer;

interface ConfigurableBundlePageSearchEntityManagerInterface
{
    public function createConfigurableBundlePageSearch(ConfigurableBundleTemplatePageSearchTransfer $configurableBundleTemplatePageSearchTransfer): void;

    public function updateConfigurableBundlePageSearch(ConfigurableBundleTemplatePageSearchTransfer $configurableBundleTemplatePageSearchTransfer): void;

    public function deleteConfigurableBundlePageSearch(ConfigurableBundleTemplatePageSearchTransfer $configurableBundleTemplatePageSearchTransfer): void;
}
