<h1>Nouveau projet pour tester Symfony 7 et révisions des bases</h1>

Site pour construire les regex plus facilement et rapidement : https://ihateregex.io/

Gérer les contraintes :
php bin/console make:validator

Dans le fichier créer par make:validator .php->
  class BanWord extends Constraint                                                                       
{
    public function __construct(
        public string $message = 'This contains a banned word "{{ banWord }}".', 
        public array $banWords = ['spam', 'virus'],
        ?array $groups = null,
        mixed $payLoad = null)
    {
        parent::__construct(null, $groups, $payLoad);
    }

}

Dans le 2ième fichier créer par make:validator Validator.php->
public function validate($value, Constraint $constraint)
    {
        /* @var BanWord $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        $value = strtolower($value);
        foreach ($constraint->banWords as $banWord) {
            if (str_contains($value, $banWord)) {
                $this->context->buildViolation($constraint->message)
            ->setParameter('{{ banWord }}', $banWord)
            ->addViolation();
            }
        }
        
    }
