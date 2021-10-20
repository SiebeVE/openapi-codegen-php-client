<?php

declare(strict_types=1);

/**
 * This file is part of the Elastic OpenAPI PHP code generator.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\OpenApi\Codegen;

use Elastic\OpenApi\Codegen\Endpoint\Builder as EndpointBuilder;
use GuzzleHttp\Client;
use RuntimeException;

/**
 * A base client builder implementation.
 */
abstract class AbstractClientBuilder
{
    protected ?Client $client;

    /**
     * Return the configured client.
     */
    abstract public function build(): AbstractClient;

    protected function connection(): Client
    {
        if ($this->client === null) {
            throw new RuntimeException(
                'Couldn\'t create a connection if no guzzle client is set.'
            );
        }

        return $this->client;
    }

    /**
     * @return array<string, mixed>
     */
    protected function configs(string $path): array
    {
        if (! file_exists($path)) {
            throw new RuntimeException(
                sprintf('Could not load configs for path \'%s\'.', $path)
            );
        }

        /** @var string $content */
        $content = file_get_contents($path);

        return json_decode($content, true);
    }

    /**
     * @return static
     */
    public function setClient(Client $client)
    {
        $this->client = $client;

        return $this;
    }

    abstract protected function endpointBuilder(): EndpointBuilder;
}
