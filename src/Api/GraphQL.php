<?php

declare(strict_types=1);

/*
 * This file is part of the Gitlab API library.
 *
 * (c) Matt Humphrey <matth@windsor-telecom.co.uk>
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gitlab\Api;

use Gitlab\Api\AbstractApi;

class GraphQL extends AbstractApi
{
    /**
     * {@inheritDoc}
     */
    protected const URI_PREFIX = '/api/';

    /**
     * @param string $query
     * @param array  $variables
     *
     * @return array
     */
    public function execute(string $query, array $variables = [])
    {
        $params = [
            'query' => $query,
        ];
        if (!empty($variables)) {
            $params['variables'] = \json_encode($variables);
        }

        return $this->post('/graphql', $params);
    }

    /**
     * @param string $file
     * @param array  $variables
     *
     * @return array
     */
    public function fromFile(string $file, array $variables = [])
    {
        return $this->execute(\file_get_contents($file), $variables);
    }
}
