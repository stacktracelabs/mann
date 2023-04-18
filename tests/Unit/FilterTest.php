<?php

use Illuminate\Support\Collection;
use StackTrace\Mann\Filter;
use StackTrace\Mann\Filterables\Select;
use StackTrace\Mann\FilterValue;
use StackTrace\Mann\Option;

it('should filter collection', function () {
    // Define filter
    $filter = new Filter();

    $filter->addFilterable(
        Select::make('Role', 'role')
            ->allowMultipleSelections()
            ->option(new Option('admin', 'Admin'))
            ->option(new Option('editor', 'Editor'))
    );

    // Create source to be filtered on
    $users = collect([
        ['name' => 'Peter', 'role' => 'admin'],
        ['name' => 'Adriána', 'role' => 'editor'],
        ['name' => 'Adam', 'role' => null],
    ]);

    // Apply filter on source.
    $results = $filter->apply($users, FilterValue::fromArray([
        'role' => ['admin'],
    ]));

    expect($results)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(1)
        ->toContain(['name' => 'Peter', 'role' => 'admin']);
});
