<?php

namespace App\Twig;

use App\Entity\User;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
  public function __construct(private HttpClientInterface $client)
  {
    
  }
  public function getFilters()
  {
    return [
      new TwigFilter('shortNumberStyle', [$this, 'formatNumber']),
      new TwigFilter('getOrderCompany', [$this, 'getCompany']),
      new TwigFilter('getOrderCustomer', [$this, 'getCustomer']),
    ];
  }

  public function formatNumber($value)
  {
    $suffix = '';

    if ($value >= 1000000000) {
      $value = $value / 1000000000;
      $suffix = 'B';
    } elseif ($value >= 1000000) {
      $value = $value / 1000000;
      $suffix = 'M';
    } elseif ($value >= 1000) {
      $value = $value / 1000;
      $suffix = 'k';
    }

    return number_format($value, $value == (int)$value ? 0 : 2, '.', ',') . $suffix;
  }


  public function getCompany($value, array $arguments)
  {
    $url = $_ENV['BASE_URL'] . '/company/' . $value;
    $response = $this->client->request(
        'GET',
        $url,
        [
            'headers' => [
                'Authorization' => 'bearer '.$arguments['token']
            ]
        ]
    );

      $statusCode = $response->getStatusCode();
      if ($statusCode == 201 || $statusCode == 200) {
          $content = $response->toArray(false);
          $company = $content['data'];
          return $company;
      } else {
          return null;
      }
  }

  public function getCustomer($value, array $arguments)
  {
    $url = $_ENV['BASE_URL'] . '/user/' . $value;
    $response = $this->client->request(
        'GET',
        $url,
        [
            'headers' => [
                'Authorization' => 'bearer '.$arguments['token']
            ]
        ]
    );

      $statusCode = $response->getStatusCode();
      if ($statusCode == 201 || $statusCode == 200) {
          $content = $response->toArray(false);
          $user = $content['data'];
          return $user;
      } else {
          return null;
      }
  }
}
