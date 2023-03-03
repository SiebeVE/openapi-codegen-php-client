<?php

declare(strict_types=1);

namespace ADS\OpenApi\Codegen\ClientBuilder;

use ADS\OpenApi\Codegen\Client\Client;
use ADS\OpenApi\Codegen\Client\ClientWrapper;
use ADS\OpenApi\Codegen\Client\GuzzleClientWrapper;
use ADS\OpenApi\Codegen\Client\SymfonyClientWrapper;
use GuzzleHttp\Client as GuzzleClient;
use RuntimeException;
use Symfony\Contracts\HttpClient\HttpClientInterface as SymfonyClient;

use function file_exists;
use function file_get_contents;
use function json_decode;
use function sprintf;

use const JSON_THROW_ON_ERROR;

/**
 * A base client builder implementation.
 */
abstract class ClientBuilder
{
    protected ClientWrapper $client;

    public function __construct(GuzzleClient|SymfonyClient|ClientWrapper $client)
    {
        $this->client = match (true) {
            $client instanceof GuzzleClient => new GuzzleClientWrapper($client),
            $client instanceof SymfonyClient => new SymfonyClientWrapper($client),
            $client instanceof ClientWrapper => $client,
        };
    }

    public function client(): ClientWrapper
    {
        return $this->client;
    }

    /**
     * Return the configured client.
     */
    abstract public function build(): Client;

    /** @return array<string, mixed> */
    protected function configs(string $path): array
    {
        if (! file_exists($path)) {
            throw new RuntimeException(
                sprintf('Could not load configs for path \'%s\'.', $path),
            );
        }

        /** @var string $content */
        $content = file_get_contents($path);

        /** @var array<string, mixed> $configs */
        $configs = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        return $configs;
    }
}
