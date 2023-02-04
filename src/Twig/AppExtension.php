<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('shortNumberStyle', [$this, 'formatNumber']),
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
}