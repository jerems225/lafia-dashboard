<?php

namespace App\Services;

use App\Entity\CountCompany;
use App\Entity\CountOrders;
use App\Entity\CountUser;
use App\Entity\StaticYear;
use App\Entity\User;
use App\Repository\CountCompanyRepository;
use App\Repository\CountOrdersRepository;
use App\Repository\CountUserRepository;
use App\Repository\StaticYearRepository;
use DateTimeImmutable;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OverviewService
{
    public const PERCENT = 100;

    /**
     *
     * @param HttpClientInterface $client
     * @param CountUserRepository $countUserRepository
     * @param CountCompanyRepository $countCompanyRepository
     * @param CountOrdersRepository $countOrdersRepository
     */
    public function __construct(
        public HttpClientInterface $client,
        private CountUserRepository $countUserRepository,
        private CountCompanyRepository $countCompanyRepository,
        private CountOrdersRepository $countOrdersRepository,
        private StaticYearRepository $staticYearRepository
    ) {
    }

    /**
     *
     * @param User $user
     * @return CountUser
     */
    public function getUsersCount(User $user): CountUser
    {
        $url = $_ENV['BASE_URL'] . '/users';
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
            $users = $content['data'];

            $countUser = $this->countUserRepository->findAll();
            $checkAt = new \DateTimeImmutable();
            if (count($countUser) == 0) {
                $countUser = new CountUser();
            } else {
                $countUser = $countUser[0];
            }

            $countUser->setCheckAt($checkAt);
            $diff = (count($users) - $countUser->getUsers());
            if ($diff > 0) {
                $percent = (self::PERCENT * $diff)  / count($users);
                $countUser->setPercent($percent);
            }

            $countUser->setUsers(count($users));

            $this->countUserRepository->save($countUser, true);
            return $countUser;
        }
    }

    /**
     *
     * @param User $user
     * @return void
     */
    public function getCompaniesCount(User $user)
    {
        $url = $_ENV['BASE_URL'] . '/companies';
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

            $countCompany = $this->countCompanyRepository->findAll();
            $checkAt = new \DateTimeImmutable();
            if (count($countCompany) == 0) {
                $countCompany = new CountCompany();
            } else {
                $countCompany = $countCompany[0];
            }

            $countCompany->setCheckAt($checkAt);
            $diff = (count($companies) - $countCompany->getCompanies());
            if ($diff > 0) {
                $percent = (self::PERCENT * $diff)  / count($companies);
                $countCompany->setPercent($percent);
            }

            $countCompany->setCompanies(count($companies));

            $this->countCompanyRepository->save($countCompany, true);
            return $countCompany;
        }
    }

    /**
     * get sum of orders amounts
     *
     * @param Array $orders
     * @return integer|float
     */
    private function getAmounts(array $orders): int|float
    {
        $amounts = [];
        foreach ($orders as $o) {
            array_push($amounts,  $o['amount']);
        }

        return array_sum($amounts);
    }

    /**
     *
     * @param Array $orders
     * @param CountOrders $countOrder
     * @return void
     */
    private function deliveryCount(array $orders, CountOrders $countOrder): void
    {
        $deliveries_amounts = [];
        foreach ($orders as $o) {
            array_push($deliveries_amounts, $o['deliveriesAmount']);
        }

        $amount = array_sum($deliveries_amounts);
        $diff = ($amount - $countOrder->getDeliveriesAmount());
        if ($diff > 0) {
            $percent = (self::PERCENT * $diff)  / $amount;
            $countOrder->setDeliveriesPercent($percent);
        }

        $countOrder->setDeliveriesAmount($amount);
    }

    /**
     *
     * @param User $user
     * @param String $status
     * @return CountOrders
     */
    public function getOrdersCount(User $user, String $status): CountOrders
    {
        $url = $_ENV['BASE_URL'] . '/orders?status=' . $status;
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

            $countOrders = $this->countOrdersRepository->findAll();
            $checkAt = new \DateTimeImmutable();
            $countOrder = count($countOrders) > 0 ? $countOrders[0] : new CountOrders();

            $countOrder->setCheckAt($checkAt);
            $countOrder->setOrders(count($orders));
            $amount = $this->getAmounts($orders);
            $diff = ($amount - $countOrder->getAmount());
            if ($diff > 0) {
                $percent = (self::PERCENT * $diff)  / $amount;
                $countOrder->setPercent($percent);
            }
            $countOrder->setAmount($amount);

            $this->deliveryCount($orders, $countOrder);

            $this->countOrdersRepository->save($countOrder, true);

            return $countOrder;
        }
    }

    public function getOrdersByMonth(User $user, String $status, String $year)
    {
        $months = [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Oct",
            "Nov",
            "Dec"
        ];

        $url = $_ENV['BASE_URL'] . '/orders?status=' . $status;
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

            //filter order by year $year from dashboard request
            $ordersYear = [];
            foreach($orders as $o)
            {
                $orderAt = new \DateTimeImmutable($o['createdAt']);
                $orderYear = $orderAt->format('Y');
                if($orderYear == $year)
                {
                    array_push($ordersYear, $o);
                }
            }

            //filter by month
            $ordersMonthCount = [];
            foreach($months as $m)
            {
                $ordersMonth = 0;
                foreach($ordersYear as $o)
                {
                    $orderAt = new \DateTimeImmutable($o['createdAt']);
                    $orderMonth = $orderAt->format('M');
                    if($orderMonth == $m)
                    {
                        $ordersMonth += 1;
                    }
                }

                array_push($ordersMonthCount, $ordersMonth);
            }

            //change current stat year
            $this->changeCurrentStatsYear($year);

            return $ordersMonthCount;
        }
    }

    /**
     *
     * @return array
     */
    public function statsYear() : array
    {
        $staticYear = $this->staticYearRepository->findAll();
        $date = new \DateTimeImmutable();
        $currentYear = $date->format('Y');

        $isExist = false;
        foreach($staticYear as $s)
        {
            if($s->getStatsYear() == $currentYear)
            {
                $isExist = true;
            }
        }

        if(!$isExist)
        {
            $year = new StaticYear();
            $year->setStatsYear($currentYear);
            $year->setIsSelect(false);

            $this->staticYearRepository->save($year,true);
        }



        return $this->staticYearRepository->findAll();
    }

    private function changeCurrentStatsYear(String $year) : void
    {
        //update the current stats date
        $years = $this->staticYearRepository->findAll();
        foreach($years as $y)
        {
            if($y->getStatsYear() == $year)
            {
                $y->setIsSelect(true);
            }
            else
            {
                $y->setIsSelect(false);
            }

            $this->staticYearRepository->save($y,true);
        }

    }

    public function getCompaniesByMonth(User $user, String|null $status, String $year)
    {
        $months = [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Oct",
            "Nov",
            "Dec"
        ];

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

            //filter order by year $year from dashboard request
            $requestsYear = [];
            foreach($companies as $c)
            {
                $createdAt = new \DateTimeImmutable($c['createdAt']);
                $requestYear = $createdAt->format('Y');
                if($requestYear == $year)
                {
                    array_push($requestsYear, $c);
                }
            }

            //filter by month
            $requestsMonthCount = [];
            foreach($months as $m)
            {
                $requestsMonth = 0;
                foreach($requestsYear as $c)
                {
                    $createdAt = new \DateTimeImmutable($c['createdAt']);
                    $requestMonth = $createdAt->format('M');
                    if($requestMonth == $m)
                    {
                        $requestsMonth += 1;
                    }
                }

                array_push($requestsMonthCount, $requestsMonth);
            }

            return $requestsMonthCount;
        }
    }


}
