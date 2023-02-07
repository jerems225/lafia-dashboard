<?php

namespace App\Services\Companies;

use App\Entity\User;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CompaniesService
{
    public function __construct(private HttpClientInterface $client)
    {
    }

    public function getCompanies(User $user, String|null $status): array
    {
        $url = $_ENV['BASE_URL'] . '/companies?status=' . $status;
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
            $companies = $content['data'];

            return  [
                'status' => true,
                'message' => $content['message'],
                'companies' => $companies
            ];
        } else {
            $content = $response->toArray(false);
            return [
                'status' => false,
                'message' => $content['message']
            ];
        }
    }

    public function getCompany(User $user, String $company_uuid): array
    {
        $url = $_ENV['BASE_URL'] . '/company/' . $company_uuid;
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
            $company = $content['data'];

            //get owner
            $owner = $this->getOwner($user, $company['ownerId']);
            if ($owner['status']) {
                return  [
                    'status' => true,
                    'message' => $content['message'],
                    'company' => $company,
                    'owner' => $owner['owner']['owner'],
                    'user' => $owner['owner']['user']
                ];
            } else {
                return [
                    'status' => false,
                    'message' => $content['message'],
                ];
            }
        } else {
            $content = $response->toArray(false);
            return [
                'status' => false,
                'message' => $content['message']
            ];
        }
    }

    private function getOwner(User $user, $user_uuid): array
    {
        $url = $_ENV['BASE_URL'] . '/owner/' . $user_uuid;
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
            $owner = $content['data'];

            return  [
                'status' => true,
                'message' => $content['message'],
                'owner' => $owner
            ];
        } else {
            $content = $response->toArray(false);
            return [
                'status' => false,
                'message' => $content['message']
            ];
        }
    }

    public function updateStatus(User $user, String $company_uuid, String $status) : Array
    {
        $url = $_ENV['BASE_URL'] . '/company/modifystatus/' . $company_uuid;
        $response = $this->client->request(
            'PUT',
            $url,
            [
                'headers' => [
                    'Authorization' => 'bearer ' . $user->getToken()
                ],
                'body' => [
                    'status' => $status,
                    'user_uuid' => $user->getUuid()
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
