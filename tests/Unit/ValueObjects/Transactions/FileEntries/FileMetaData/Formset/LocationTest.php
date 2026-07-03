<?php

use Noardcode\LaravelSignhost\ValueObjects\Transactions\FileEntries\FileMetaData\FormSet\Location;

it('can be created', function () {
    $location = new Location(
        'search',
        1,
        10,
        20,
        30,
        40,
        100,
        50,
        2
    );

    expect($location->getSearch())->toBe('search')
        ->and($location->getOccurence())->toBe(1)
        ->and($location->getTop())->toBe(10)
        ->and($location->getRight())->toBe(20)
        ->and($location->getBottom())->toBe(30)
        ->and($location->getLeft())->toBe(40)
        ->and($location->getWidth())->toBe(100)
        ->and($location->getHeight())->toBe(50)
        ->and($location->getPageNumber())->toBe(2);

    $this->assertEquals([
        'Search' => 'search',
        'Occurence' => 1,
        'Top' => 10,
        'Right' => 20,
        'Bottom' => 30,
        'Left' => 40,
        'Width' => 100,
        'Height' => 50,
        'PageNumber' => 2,
    ], $location->toArray());
});
