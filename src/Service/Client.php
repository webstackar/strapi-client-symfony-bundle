<?php
/**
 * Webstackar - Expert Magento & Développement PHP
 *
 * @author Harouna MADI <harouna@webstackar.fr>
 * @link https://webstackar.fr
 * @copyright Copyright (c) 2023 Webstackar Nantes
 */

namespace Webstackar\StrapiClientBundle\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Webstackar\StrapiClientBundle\Exception\RequestException;
use Webstackar\StrapiClientBundle\Exception\ResponseException;
use Webstackar\StrapiClientBundle\Request\Query;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webstackar\StrapiClientBundle\Response\Collection;
use Webstackar\StrapiClientBundle\Response\Entry;

class Client
{

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly ParameterBagInterface $parameters,
        private readonly TranslatorInterface $translator
    ){}

    /**
     * @throws RequestException
     * @throws ResponseException
     */
    public function getEntry(string $type, string|int|null $id = null, ?Query $query = null): Entry
    {
        $result = $this->sendRequest('GET', $this->getApiUrl() . "$type/$id", [
            'query' => $query?->getParams()
        ]);
        return new Entry($result['data']);
    }

    /**
     * @throws RequestException
     * @throws ResponseException
     */
    public function getEntries(string $type, ?Query $query = null): Collection
    {
        $result = $this->sendRequest('GET', $this->getApiUrl() . $type, [
            'query' => $query?->getParams()
        ]);
        return new Collection(data: $result['data']??$result, meta: $result['meta']??[]);
    }

    /**
     * @throws RequestException
     * @throws ResponseException
     */
    public function create(string $type, array $data): array
    {
        return $this->sendRequest('POST', $this->getApiUrl() . "$type", [
            'json' => $data
        ]);
    }

    /**
     * @throws RequestException
     * @throws ResponseException
     */
    public function update(string $type, string|int $id, array $data): array
    {
        return $this->sendRequest('PUT', $this->getApiUrl() . "$type/$id", [
            'json' => $data
        ]);
    }

    /**
     * @throws RequestException
     * @throws ResponseException
     */
    public function delete(string $type, string|int $id): array
    {
        return $this->sendRequest('DELETE', $this->getApiUrl() . "$type/$id");
    }


    /**
     * @throws RequestException
     * @throws ResponseException
     */
    public function sendRequest(string $method, string $uri, array $options = []): array
    {
        if (!isset($options['headers'])) {
            $options['headers'] = [];
        }
        $options['headers'] = array_merge($options['headers'], [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->getApiToken()
        ]);

        try {
            $response = $this->client->request($method, $uri, $options);
            $responseArray = $response->toArray();
            if (!in_array($response->getStatusCode(), [200, 201])) {
                throw new ResponseException(
                    $this->translator->trans('Strapi client response error "%name%": %msg%', [
                        '%name%' => $responseArray['error']['name'] ?? 'Unknown',
                        '%msg%' => $responseArray['error']['message'] ?? 'Unknown error message',
                    ]),
                    $response->getStatusCode()
                );
            }
        } catch (ResponseException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new RequestException(
                $this->translator->trans('Strapi client request error: %msg%', [
                    '%msg%' => $e->getMessage()
                ]),
                $e->getCode(),
                $e
            );
        }

        return $responseArray;
    }

    public function getApiUrl(): string
    {
        return rtrim($this->parameters->get('webstackar.strapi_client.api_url'), '/') . '/';
    }

    public function getApiToken(): ?string
    {
        return $this->parameters->get('webstackar.strapi_client.api_token');
    }
}
