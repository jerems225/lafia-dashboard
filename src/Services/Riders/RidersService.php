<?php

namespace App\Services\Riders;

use App\Entity\User;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RidersService
{
    public function __construct(private HttpClientInterface $client)
    {
        
    }

    /**
     * Get All riders by status
     *
     * @param User $user
     * @param String|null $status
     * @return Array
     */
    public function getRiders(User $user) : Array
    {
        $url = $_ENV['BASE_URL'] . '/allriders';
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
            $riders = $content['data'];
            
            return  [
                'status' => true,
                'message' => $content['message'],
                'riders' => $riders
            ];
        } else {
            $content = $response->toArray(false);
            return [
                'status' => false,
                'message' => $content['message']
            ];
        }
    }

    /**
     * Get Rider By Uuid
     *
     * @param User $user
     * @param String $user_uuid
     * @return array
     */
    public function getRider(User $user, String $user_uuid) : array
    {
        $url = $_ENV['BASE_URL'] . '/rider/'. $user_uuid;
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
            $rider = $content['data'];

            return  [
                'status' => true,
                'message' => $content['message'],
                'rider' => $rider
            ];
        } else {
            $content = $response->toArray(false);
            return [
                'status' => false,
                'message' => $content['message']
            ];
        }
        return [];
    }

    /**
     * Update Rider Status
     *
     * @param User $user
     * @param String $user_uuid
     * @param String $status
     * @return Array
     */
    public function updateStatus(User $user, String $user_uuid, String $status) : Array
    {
        $url = $_ENV['BASE_URL'] . '/rider/modify/' . $user_uuid;
        $response = $this->client->request(
            'PUT',
            $url,
            [
                'headers' => [
                    'Authorization' => 'bearer ' . $user->getToken()
                ],
                'body' => [
                    'status' => $status,
                ]
            ]
        );

        $statusCode = $response->getStatusCode();
        if ($statusCode == 201 || $statusCode == 200) {
            $content = $response->toArray(false);
            return  [
                'status' => true,
                'message' => $content['message'],
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