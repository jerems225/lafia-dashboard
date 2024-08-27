<?php

namespace App\Services\Orders;

use App\Entity\User;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PromoCodeService
{
    public function __construct(private HttpClientInterface $client)
    {
        
    }

    public function getCodes(User $user) : array
    {
        $url = $_ENV['BASE_URL'] . '/promo-codes?user_uuid='.$user->getUuid();
        $response = $this->client->request(
            'GET',
            $url,
            [
                'headers' => [
                    'Authorization' => 'bearer ' . $user->getToken()
                ]
            ]
        );

        $statusCode = $response->getStatusCode();
        if ($statusCode == 201 || $statusCode == 200) {
            $content = $response->toArray(false);
            $promoCodes = $content['data'];
            return  [
                'status' => true,
                'message' => $content['message'],
                'promo-codes' => $promoCodes
            ];
        } else {
            $content = $response->toArray(false);
            return [
                'status' => false,
                'message' => $content['message']
            ];
        }
    }

    public function getCode(User $user, String $code_uuid) : array
    {
        $url = $_ENV['BASE_URL'] . '/promo-code/'.$code_uuid.'/'.$user->getUuid();
        $response = $this->client->request(
            'GET',
            $url,
            [
                'headers' => [
                    'Authorization' => 'bearer ' . $user->getToken()
                ]
            ]
        );

        $statusCode = $response->getStatusCode();
        if ($statusCode == 201 || $statusCode == 200) {
            $content = $response->toArray(false);
            $promoCode = $content['data'];
            return  [
                'status' => true,
                'message' => $content['message'],
                'promo-code' => $promoCode
            ];
        } else {
            $content = $response->toArray(false);
            return [
                'status' => false,
                'message' => $content['message']
            ];
        }
    }

    public function addCodes(User $user, String $code, Int $percent) : array
    {
        $url = $_ENV['BASE_URL'] . '/promo-code/create';
        $response = $this->client->request(
            'POST',
            $url,
            [
                'headers' => [
                    'Authorization' => 'bearer ' . $user->getToken()
                ],
                'body' => [
                    'code' => $code,
                    'promoPercent' => $percent,
                    'userId' => $user->getUuid()
                ]
            ]
        );

        $statusCode = $response->getStatusCode();
        if ($statusCode == 201 || $statusCode == 200) {
            $content = $response->toArray(false);
            $promoCode = $content['data'];
            return  [
                'status' => true,
                'message' => $content['message'],
                'promo-code' => $promoCode
            ];
        } else {
            $content = $response->toArray(false);
            return [
                'status' => false,
                'message' => $content['message']
            ];
        }
    }

    public function updateCode(User $user, String $code_uuid, String $code, Int $percent) : array
    {
        $url = $_ENV['BASE_URL'] . '/promo-code/modify/'.$code_uuid;
        $response = $this->client->request(
            'PUT',
            $url,
            [
                'headers' => [
                    'Authorization' => 'bearer ' . $user->getToken()
                ],
                'body' => [
                    'code' => $code,
                    'promoPercent' => $percent,
                    'userId' => $user->getUuid()
                ]
            ]
        );

        $statusCode = $response->getStatusCode();
        if ($statusCode == 201 || $statusCode == 200) {
            $content = $response->toArray(false);
            $promoCode = $content['data'];
            return  [
                'status' => true,
                'message' => $content['message'],
                'promo-code' => $promoCode
            ];
        } else {
            $content = $response->toArray(false);
            return [
                'status' => false,
                'message' => $content['message']
            ];
        }
    }
    public function deleteCode(User $user, String $code_uuid) : array
    {
        $url = $_ENV['BASE_URL'] . '/promo-code/delete/'.$code_uuid.'/'.$user->getUuid();
        $response = $this->client->request(
            'DELETE',
            $url,
            [
                'headers' => [
                    'Authorization' => 'bearer ' . $user->getToken()
                ],
            ]
        );

        $statusCode = $response->getStatusCode();
        if ($statusCode == 201 || $statusCode == 200) {
            $content = $response->toArray(false);
            $promoCode = $content['data'];
            return  [
                'status' => true,
                'message' => $content['message'],
                'promo-code' => $promoCode
            ];
        } else {
            $content = $response->toArray(false);
            return [
                'status' => false,
                'message' => $content['message']
            ];
        }
    }
}