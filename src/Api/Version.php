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

class Version extends AbstractApi
{
    /**
     * Retrieve version information for the GitLab instance.
     *
     * Note: `GET /version` is documented together with `GET /metadata` on the
     * Metadata API page, which has no dedicated per-endpoint anchor.
     *
     * @see https://docs.gitlab.com/api/metadata/
     */
    public function show(): mixed
    {
        return $this->get('version');
    }
}
