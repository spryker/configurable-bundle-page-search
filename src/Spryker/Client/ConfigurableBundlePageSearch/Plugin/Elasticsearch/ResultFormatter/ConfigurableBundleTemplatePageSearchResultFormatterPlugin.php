<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ConfigurableBundlePageSearch\Plugin\Elasticsearch\ResultFormatter;

use Elastica\ResultSet;
use Generated\Shared\Search\PageIndexMap;
use Generated\Shared\Transfer\ConfigurableBundleTemplatePageSearchTransfer;
use Generated\Shared\Transfer\ConfigurableBundleTemplateTransfer;
use Spryker\Client\Search\Plugin\Elasticsearch\ResultFormatter\AbstractElasticsearchResultFormatterPlugin;

class ConfigurableBundleTemplatePageSearchResultFormatterPlugin extends AbstractElasticsearchResultFormatterPlugin
{
    public const NAME = 'ConfigurableBundleTemplateCollection';

    /**
     * @api
     *
     * @return string
     */
    public function getName(): string
    {
        return static::NAME;
    }

    /**
     * @param \Elastica\ResultSet $searchResult
     * @param array $requestParameters
     *
     * @return array
     */
    protected function formatSearchResult(ResultSet $searchResult, array $requestParameters): array
    {
        $configurableBundleTemplatePageSearchTransfers = [];

        foreach ($searchResult->getResults() as $document) {
            $configurableBundleTemplatePageSearchTransfers[] = $this->mapToTransfer(
                $document->getSource()[PageIndexMap::SEARCH_RESULT_DATA]
            );
        }

        return $configurableBundleTemplatePageSearchTransfers;
    }

    /**
     * @param array $data
     *
     * @return \Generated\Shared\Transfer\ConfigurableBundleTemplatePageSearchTransfer
     */
    protected function mapToTransfer(array $data): ConfigurableBundleTemplatePageSearchTransfer
    {
        $productConcretePageSearchTransfer = new ConfigurableBundleTemplatePageSearchTransfer();
        $productConcretePageSearchTransfer->fromArray($data, true);
        $productConcretePageSearchTransfer->setFkConfigurableBundleTemplate($data[ConfigurableBundleTemplateTransfer::ID_CONFIGURABLE_BUNDLE_TEMPLATE]);

        return $productConcretePageSearchTransfer;
    }
}
