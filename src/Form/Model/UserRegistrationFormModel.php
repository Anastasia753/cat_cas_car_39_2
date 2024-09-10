<?php

namespace App\Form\Model;

use App\Validator\UniqueUser;
use Symfony\Component\Validator\Constraints as Assert;

class UserRegistrationFormModel
{

    #[Assert\NotBlank()]
    #[Assert\Email()]
    #[UniqueUser()]
    public $email;

    public $firstName;

    #[Assert\Length(min: '6', minMessage: 'Пароль должен быть не менее 6-ти символов')]
    #[Assert\NotBlank(message: 'Пароль не указан')]
    public $plainPassword;

    #[Assert\IsTrue(message: 'Вы должны согласиться с условиями')]
    public $agreeTerms;
}