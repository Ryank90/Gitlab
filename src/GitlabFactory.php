<?php

namespace ServiceMap\Gitlab;

use Gitlab\Client;
use InvalidArgumentException;

class GitlabFactory
{
  /**
   * Make a new Gitlab client.
   *
   * @param array $config
   *
   * @return \Gitlab\Client
   */
  public function make(array $config)
  {
    $config = $this->getConfig($config);

    return $this->getClient($config);
  }

  /**
   * Get the configuration data.
   *
   * @param string[] $config
   *
   * @throws \InvalidArgumentException
   *
   * @return array
   */
  protected function getConfig(array $config)
  {
    $keys = ['token', 'base_url'];

    foreach ($keys as $key) {
      if (!array_key_exists($key, $config)) {
        throw new InvalidArgumentException('Missing configuration key [' . $key . '].');
      }
    }

    return array_only($config, ['token', 'base_url', 'method', 'sudo']);
  }

  /**
   * Get the main client.
   *
   * @param array $config
   *
   * @return \Gitlab\Client
   */
  protected function getClient(array $config)
  {
    $client = new Client($config['base_url']);

    $client->authenticate(
      $config['token'],
      array_get($config, 'method', Client::AUTH_HTTP_TOKEN),
      array_get($config, 'sudo', null)
    );

    return $client;
  }
}
