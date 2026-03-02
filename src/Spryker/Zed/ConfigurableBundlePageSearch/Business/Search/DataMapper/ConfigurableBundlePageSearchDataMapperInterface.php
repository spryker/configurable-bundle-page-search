<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ConfigurableBundlePageSearch\Business\Search\DataMapper;

use Generated\Shared\Transfer\ConfigurableBundleTemplatePageSearchTransfer;
use Generated\Shared\Transfer\LocaleTransfer;

interface ConfigurableBundlePageSearchDataMapperInterface
{
    public function mapConfigurableBundleTemplatePageSearchTransferToSearchData(
        ConfigurableBundleTemplatePageSearchTransfer $configurableBundleTemplatePageSearchTransfer,
        LocaleTransfer $localeTransfer
    ): array;
}
