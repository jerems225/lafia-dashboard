<?php

namespace App\Services\Companies;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\NativeHttpClient;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;

class CategorieService extends AbstractController
{
    public function __construct(private HttpClientInterface $client)
    {
    }

    /**
     * Get All Companies Categories
     *
     * @param User $user
     * @return array|null
     */
    public function getCategories(User $user): array|null
    {
        $url = $_ENV['BASE_URL'] . '/categories-companies';
        $response = $this->client->request(
            'GET',
            $url,
            [
                'headers' => [
                    'Authorization' => 'bearer ' . $user->getToken()
                ]
            ]
        );

        $statusCode =  $response->getStatusCode();
        if ($statusCode == 201 || $statusCode == 200) {
            $content = $response->toArray(false);
            $categories = $content['data'];

            return [
                'status' => true,
                'message' => $content['message'],
                'categories' => $categories
            ];
        } else {
            $content = $response->toArray(false);
            return [
                'status' => true,
                'message' => $content['message']
            ];;
        }
    }


    /**
     * Create New Category
     *
     * @param User $user
     * @param array $category
     * @return Array
     */
    public function createCategory(User $user, array $category): Array
    {
        $image = $category['image'];
        $url = $_ENV['BASE_URL'] . '/category-company/create';
        $response = $this->client->request(
            'POST',
            $url,
            [
                'headers' => [
                    'Authorization' => 'bearer ' . $user->getToken(),
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode([
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'userId' => $user->getUuid()
                ])

            ]
        );

        $statusCode = $response->getStatusCode();
        if ($statusCode == 201 || $statusCode == 200) {
            $content = $response->toArray(false);
            $category = $content['data'];

            //upload Image
            $upload = $this->uploadCategoryImage($user, $category, $image);

            //if upload not work delete category
            return  $upload['status'] ? ['status' => true, 'message' => $content['message']] : ['status' => false, 'message' => $upload['message']];
        } else {
            $content = $response->toArray(false);
            return [
                'status' => false,
                'message' => $content['message']
            ];
        }
    }

    /**
     * Upload Categorie Image
     *
     * @param User $user
     * @param array $category
     * @param Object $image
     * @return Array
     */
    private function uploadCategoryImage(User $user, array $category, Object $image): array
    {
        //get the real image

        $formFields = [
            'image' => DataPart::fromPath($image->getPathname(), $image->getClientOriginalName()),
        ];
        $client = new NativeHttpClient(['base_uri' => $_ENV['BASE_URL']]);
        $formData = new FormDataPart($formFields);
        $contentType = trim(explode(":", $formData->getPreparedHeaders()->toArray()[0])[1]);;
        $url = $_ENV['BASE_URL'] . '/category-company/images/upload/' . $category['_id'];
        $response = $client->request(
            'POST',
            $url,
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => $contentType,
                    'Authorization' => 'bearer ' . $user->getToken(),
                ],
                'body' => $formData->bodyToIterable()
            ]
        );


        $statusCode = $response->getStatusCode();
        if ($statusCode == 201 || $statusCode == 200) {
            $content = $response->toArray(false);
            return [
                'status' => true,
                'message' => $content['message']
            ];
        } else {
            $content = $response->toArray(false);
            $deleteCategory = $this->actionDeleteCategory($user, $category);
            return [
                'status' => false,
                'message' => $content['message']
            ];
        }
    }

    /**
     * Action delete Category
     *
     * @param User $user
     * @param Array $category
     * @return boolean
     */
    private function actionDeleteCategory(User $user, Array $category) : bool
    {
        $url = $_ENV['BASE_URL'] . '/category-company/delete/'.$category['_id'];
        $response = $this->client->request(
            'DELETE',
            $url,
            [
                'headers' => [
                    'Authorization' => 'bearer ' . $user->getToken(),
                ],
            ]
        );

        $statusCode = $response->getStatusCode();
        return $statusCode == 201 || $statusCode == 200 ? true : false;
    }
}
