<?php

namespace App\Service;

class IsbnGenerator
{
  public static function generate(): int
  {
    $isbn = (string) rand(978, 979);

    $countryCodes = [
      0, 1, 2, 10, 3, 4, 5, 7, 81, 93, 82, 83, 85, 87, 88, 89, 90, 94, 91, 950, 951, 952, 956, 972, 989, 957, 986, 960, 962, 988, 963, 964, 965, 967, 987, 979, 981, 9971, 9946, 9979, 99915, 99923, 99929, 99937, 99951, 99953
    ];

    $isbn .= (string) $countryCodes[array_rand($countryCodes)];

    $isbnLength = strlen($isbn);

    $thirdGroup = rand(10, pow(10, 11 - $isbnLength) - 1);

    $isbn .= $thirdGroup;

    $isbnLength = strlen($isbn);

    $fourthGroupLength = 12 - $isbnLength;

    $fourthGroupMaxValue = pow(10, $fourthGroupLength) - 1;

    $fourthGroup = rand(0, $fourthGroupMaxValue);
    $fourthGroup = sprintf("%0".$fourthGroupLength."d", $fourthGroup);

    $isbn .= $fourthGroup;

    $lastDigit = (10 - (($isbn[0] + $isbn[2] + $isbn[4] + $isbn[6] + $isbn[8] + $isbn[10] + 3 * ($isbn[1] + $isbn[3] + $isbn[5] + $isbn[7] + $isbn[9] + $isbn[11])) % 10)) % 10;

    return (int) $isbn.$lastDigit;
  }
}