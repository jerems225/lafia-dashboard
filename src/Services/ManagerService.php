<?php

namespace App\Services;

use App\Entity\User;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ManagerService
{
    public function __construct(private HttpClientInterface $client)
    {
        
    }

    public function getAdmins(User $user) : array
    {
        $url = $_ENV['BASE_URL'] . '/admins?user_uuid='.$user->getUuid();
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
            $admins = $content['data'];
            return  [
                'status' => true,
                'message' => $content['message'],
                'admins' => $admins
            ];
        } else {
            $content = $response->toArray(false);
            return [
                'status' => false,
                'message' => $content['message']
            ];
        }
    }

    public function getAdmin(User $user, $admin_uuid) : array
    {
        $url = $_ENV['BASE_URL'] . '/admin/'.$admin_uuid.'/'.$user->getUuid();
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
            $admin = $content['data'];
            return  [
                'status' => true,
                'message' => $content['message'],
                'admin' => $admin
            ];
        } else {
            $content = $response->toArray(false);
            return [
                'status' => false,
                'message' => $content['message']
            ];
        }
    }

    public function addAdmin(User $user, Array $admin)
    {
        $url = $_ENV['BASE_URL'] . '/admin/create/'.$user->getUuid();
        $response = $this->client->request(
            'POST',
            $url,
            [
                'headers' => [
                    'Authorization' => 'bearer ' . $user->getToken()
                ],
                'body' => $admin
            ]
        );

        $statusCode = $response->getStatusCode();
        if ($statusCode == 201 || $statusCode == 200) {
            $content = $response->toArray(false);
            $admin = $content['data'];
            return  [
                'status' => true,
                'message' => $content['message'],
                'admin' => $admin
            ];
        } else {
            $content = $response->toArray(false);
            return [
                'status' => false,
                'message' => $content['message']
            ];
        }
    }

    public function updateAdmin(User $user, Array $admin , String $admin_uuid) : Array
    {
        $url = $_ENV['BASE_URL'] . '/admin/modify/' . $admin_uuid;
        $response = $this->client->request(
            'PUT',
            $url,
            [
                'headers' => [
                    'Authorization' => 'bearer ' . $user->getToken()
                ],
                'body' => $admin
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

    public function updatePassword(User $user, Array $admin , String $admin_uuid) : Array
    {
        $url = $_ENV['BASE_URL'] . '/admin/modify/password/' . $admin_uuid;
        $response = $this->client->request(
            'PUT',
            $url,
            [
                'headers' => [
                    'Authorization' => 'bearer ' . $user->getToken()
                ],
                'body' => $admin
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