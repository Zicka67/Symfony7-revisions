<h1>Nouveau projet pour tester Symfony 7 et révisions des bases</h1>

Site pour construire les regex plus facilement et rapidement : https://ihateregex.io/

Gérer les contraintes :
php bin/console make:validator

Dans le fichier créer par make:validator .php->
Bien penser à mettre les mots ban dans le tableau et le parent du construct
                                                                      
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
Une boucle pour parcourir les mots ban dans la contrainte

public function validate($value, Constraint $constraint)
    {
    
        $value = strtolower($value);
        foreach ($constraint->banWords as $banWord) {
            if (str_contains($value, $banWord)) {
                $this->context->buildViolation($constraint->message)
            ->setParameter('{{ banWord }}', $banWord)
            ->addViolation();
            }
        }
        
    }
