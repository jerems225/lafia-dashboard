<?php

namespace App\Services\Orders;

use App\Entity\User;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OrdersService
{
    public function __construct(private HttpClientInterface $client)
    {
        
    }

    /**
     * Get all orders
     *
     * @param User $user
     * @return array
     */
    public function getOrders(User $user) : array
    {
        $url = $_ENV['BASE_URL'] . '/orders';
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
            $orders = $content['data'];
            return  [
                'status' => true,
                'message' => $content['message'],
                'orders' => $orders
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