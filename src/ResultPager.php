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

namespace Gitlab;

use Closure;
use Generator;
use Gitlab\Api\AbstractApi;
use Gitlab\Exception\RuntimeException;
use Gitlab\HttpClient\Message\ResponseMediator;
use ValueError;

/**
 * This is the result pager class.
 *
 * @author Ramon de la Fuente <ramon@future500.nl>
 * @author Mitchel Verschoof <mitchel@future500.nl>
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
final class ResultPager implements ResultPagerInterface
{
    /**
     * The default number of entries to request per page.
     *
     * @var int
     */
    private const PER_PAGE = 50;

    private readonly Client $client;

    private readonly int $perPage;

    /**
     * @var array<string,string>
     */
    private array $pagination;

    public function __construct(Client $client, ?int $perPage = null)
    {
        if (null !== $perPage && ($perPage < 1 || $perPage > 100)) {
            throw new ValueError(\sprintf('%s::__construct(): Argument #2 ($perPage) must be between 1 and 100, or null', self::class));
        }

        $this->client = $client;
        $this->perPage = $perPage ?? self::PER_PAGE;
        $this->pagination = [];
    }

    /**
     * Fetch a single result from an api call.
     *
     * @throws \Http\Client\Exception
     */
    public function fetch(AbstractApi $api, string $method, array $parameters = []): array
    {
        $result = self::bindPerPage($api, $this->perPage)->$method(...$parameters);

        if (!\is_array($result)) {
            throw new RuntimeException('Pagination of this endpoint is not supported.');
        }

        $this->postFetch();

        return $result;
    }

    /**
     * Fetch all results from an api call.
     *
     * @throws \Http\Client\Exception
     */
    public function fetchAll(AbstractApi $api, string $method, array $parameters = []): array
    {
        return \iterator_to_array($this->fetchAllLazy($api, $method, $parameters));
    }

    /**
     * Lazily fetch all results from an api call.
     *
     * @throws \Http\Client\Exception
     */
    public function fetchAllLazy(AbstractApi $api, string $method, array $parameters = []): Generator
    {
        /** @var mixed $value */
        foreach ($this->fetch($api, $method, $parameters) as $value) {
            yield $value;
        }

        while ($this->hasNext()) {
            /** @var mixed $value */
            foreach ($this->fetchNext() as $value) {
                yield $value;
            }
        }
    }

    /**
     * Check to determine the availability of a next page.
     */
    public function hasNext(): bool
    {
        return isset($this->pagination['next']);
    }

    /**
     * Fetch the next page.
     *
     * @throws \Http\Client\Exception
     */
    public function fetchNext(): array
    {
        return $this->get('next');
    }

    /**
     * Check to determine the availability of a previous page.
     */
    public function hasPrevious(): bool
    {
        return isset($this->pagination['prev']);
    }

    /**
     * Fetch the previous page.
     *
     * @throws \Http\Client\Exception
     */
    public function fetchPrevious(): array
    {
        return $this->get('prev');
    }

    /**
     * Fetch the first page.
     *
     * @throws \Http\Client\Exception
     */
    public function fetchFirst(): array
    {
        return $this->get('first');
    }

    /**
     * Fetch the last page.
     *
     * @throws \Http\Client\Exception
     */
    public function fetchLast(): array
    {
        return $this->get('last');
    }

    /**
     * Refresh the pagination property.
     */
    private function postFetch(): void
    {
        $response = $this->client->getLastResponse();

        $this->pagination = null === $response ? [] : ResponseMediator::getPagination($response);
    }

    /**
     * @throws \Http\Client\Exception
     */
    private function get(string $key): array
    {
        $pagination = $this->pagination[$key] ?? null;

        if (null === $pagination) {
            return [];
        }

        $result = $this->client->getHttpClient()->get($pagination);

        $content = ResponseMediator::getContent($result);

        if (!\is_array($content)) {
            throw new RuntimeException('Pagination of this endpoint is not supported.');
        }

        $this->postFetch();

        return $content;
    }

    private static function bindPerPage(AbstractApi $api, int $perPage): AbstractApi
    {
        /** @var Closure(AbstractApi): AbstractApi */
        $closure = Closure::bind(static function (AbstractApi $api) use ($perPage): AbstractApi {
            $clone = clone $api;

            $clone->perPage = $perPage;

            return $clone;
        }, null, AbstractApi::class);

        return $closure($api);
    }
}
