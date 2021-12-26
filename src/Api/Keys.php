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

class Keys extends AbstractApi
{
    /**
     * @param int $id
     *
     * @return mixed
     */
    public function show(int $id)
    {
        return $this->get('keys/'.self::encodePath($id));
    }
}
