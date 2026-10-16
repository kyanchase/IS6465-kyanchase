<?php

// Lab 2C: Constructors, Constants, and Inheritance

echo '<h1>Lab 2C: User and Tutor Classes</h1>';

function cleanText($value)
{
    return trim($value);
}

class User
{
    public const STATUS_ACTIVE = 'active';

    public $name;
    public $email;
    public $status;

    public function __construct($name, $email)
    {
        $this->name = cleanText($name);
        $this->email = cleanText(strtolower($email));
        $this->status = self::STATUS_ACTIVE;
    }
}

class Tutor extends User
{
    public $subject;

    public function __construct($name, $email, $subject)
    {
        parent::__construct($name, $email);
        $this->subject = cleanText($subject);
    }

    public function profileSummary()
    {
        return "$this->name | $this->email | $this->subject";
    }   
}

// TODO: Create a Tutor object using sample data.
$tutor = new Tutor('  Maria Chen  ', '  MARIA@EXAMPLE.COM  ', '  Mathematics  ');
// TODO: Display profileSummary().
echo $tutor->profileSummary() . '<br>';