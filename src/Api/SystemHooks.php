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

class SystemHooks extends AbstractApi
{
    /**
     * @return mixed
     */
    public function all()
    {
        return $this->get('hooks');
    }

    /**
     * @param string $url
     *
     * @return mixed
     */
    public function create(string $url)
    {
        return $this->post('hooks', [
            'url' => $url,
        ]);
    }

    /**
     * @param int $id
     *
     * @return mixed
     */
    public function test(int $id)
    {
        return $this->get('hooks/'.self::encodePath($id));
    }

    /**
     * @param int $id
     *
     * @return mixed
     */
    public function remove(int $id)
    {
        return $this->delete('hooks/'.self::encodePath($id));
    }
}
