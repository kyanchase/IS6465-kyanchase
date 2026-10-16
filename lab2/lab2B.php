<?php

// Lab 2B: PHP Functions and Basic Objects

echo '<h1>Lab 2B: Functions and Objects</h1>';

// 1. Function with Parameters and Return Value
echo '<h2>1. Function with Parameters and Return Value</h2>';

function formatName($first, $middle, $last)
{
    $first = ucfirst(strtolower(trim($first)));
    $middle = ucfirst(strtolower(trim($middle)));
    $last = ucfirst(strtolower(trim($last)));

    return "$first $middle $last";
}

// TODO: Call formatName() using sample data.
$fullName = formatName('mARIA', 'aNN', 'cHEN');
echo "$fullName<br>";

// 2. Function That Returns an Array
echo '<h2>2. Function That Returns an Array</h2>';

function cleanNameParts($first, $middle, $last)
{
    return [
        ucfirst(strtolower(trim($first))),
        ucfirst(strtolower(trim($middle))),
        ucfirst(strtolower(trim($last)))
    ];
}

// TODO: Call cleanNameParts() and display one item from the array.
$nameParts = cleanNameParts('mARIA', 'aNN', 'cHEN');
echo $nameParts[0] . '<br>';

// 3. Variable Scope
echo '<h2>3. Variable Scope</h2>';

$first = 'Maria';
$last = 'Chen';

function buildFullName()
{
    global $first, $last;

    return "$first $last";
}

echo buildFullName() . '<br>';

function buildFullName2($first, $last)
{
    return "$first $last";
}

echo buildFullName2($first, $last) . '<br>';

// 4. Static Variable
echo '<h2>4. Static Variable</h2>';

function nextTicketNumber()
{
    static $ticket = 0;

    $ticket++;

    return $ticket;
}

// TODO: Call the function at least twice.
echo nextTicketNumber() . '<br>';
echo nextTicketNumber() . '<br>';
echo nextTicketNumber() . '<br>';

// 5. User Class
echo '<h2>5. User Class</h2>';

class User
{
    public $name;
    public $email;

    public function display()
    {
        return $this->name;
    }
}

// TODO: Create a User object.
// TODO: Assign a name and email.
// TODO: Display the result of the display() method.
$user = new User();

$user->name = 'Maria Chen';
$user->email = 'maria@example.com';

echo $user->display() . '<br>';

// 6. Constructor and Password Hashing
echo '<h2>6. Constructor and Password Hashing</h2>';

class User2
{
    public $name;
    private $passwordHash;

    public function __construct($name, $password)
    {
        $this->name = $name;
        $this->passwordHash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );
    }
}

// TODO: Create one User2 object and display it using print_r().
$user2 = new User2('Maria Chen', 'samplePassword123!');

echo '<pre>';
print_r($user2);
echo '</pre>';
