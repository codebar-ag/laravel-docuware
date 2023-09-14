<?php

namespace CodebarAg\DocuWare\Connectors;

use CodebarAg\DocuWare\DTO\Configuration;
use Saloon\Http\Connector;

class DocuWareConnector extends Connector
{
	public function __construct(protected ?Configuration $configuration)
	{

	}

	/**
	 * @throws \Exception
	 */
	public function resolveBaseUrl(): string
	{
		return $this->configuration->url . '/DocuWare/Platform';
	}

	public function defaultHeaders(): array
	{
		return [
			'Accept' => 'application/json',
		];
	}

	public function defaultConfig(): array
	{
		return [
			'timeout' => $this->configuration->timeout,
			'cookies' => $this->configuration->cookie_jar,
		];
	}

}
