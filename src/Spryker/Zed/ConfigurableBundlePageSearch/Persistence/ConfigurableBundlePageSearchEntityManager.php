<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ConfigurableBundlePageSearch\Persistence;

use Generated\Shared\Transfer\ConfigurableBundleTemplatePageSearchTransfer;
use Orm\Zed\ConfigurableBundlePageSearch\Persistence\SpyConfigurableBundleTemplatePageSearch;
use Spryker\Zed\Kernel\Persistence\AbstractEntityManager;

/**
 * @method \Spryker\Zed\ConfigurableBundlePageSearch\Persistence\ConfigurableBundlePageSearchPersistenceFactory getFactory()
 */
class ConfigurableBundlePageSearchEntityManager extends AbstractEntityManager implements ConfigurableBundlePageSearchEntityManagerInterface
{
    public function createConfigurableBundlePageSearch(ConfigurableBundleTemplatePageSearchTransfer $configurableBundleTemplatePageSearchTransfer): void
    {
        $configurableBundleTemplatePageSearchEntity = new SpyConfigurableBundleTemplatePageSearch();
        $configurableBundleTemplatePageSearchEntity->fromArray($configurableBundleTemplatePageSearchTransfer->toArray());

        $configurableBundleTemplatePageSearchEntity->save();
    }

    public function updateConfigurableBundlePageSearch(ConfigurableBundleTemplatePageSearchTransfer $configurableBundleTemplatePageSearchTransfer): void
    {
        $configurableBundleTemplatePageSearchEntity = $this->getFactory()
            ->getConfigurableBundlePageSearchQuery()
            ->findOneByIdConfigurableBundleTemplatePageSearch($configurableBundleTemplatePageSearchTransfer->getIdConfigurableBundleTemplatePageSearch());

        if (!$configurableBundleTemplatePageSearchEntity) {
            return;
        }

        $configurableBundleTemplatePageSearchEntity->fromArray($configurableBundleTemplatePageSearchTransfer->toArray());
        $configurableBundleTemplatePageSearchEntity->save();
    }

    public function deleteConfigurableBundlePageSearch(ConfigurableBundleTemplatePageSearchTransfer $configurableBundleTemplatePageSearchTransfer): void
    {
        $configurableBundleTemplatePageSearchEntity = $this->getFactory()
            ->getConfigurableBundlePageSearchQuery()
            ->findOneByIdConfigurableBundleTemplatePageSearch($configurableBundleTemplatePageSearchTransfer->getIdConfigurableBundleTemplatePageSearch());

        if (!$configurableBundleTemplatePageSearchEntity) {
            return;
        }

        $configurableBundleTemplatePageSearchEntity->delete();
    }
}
