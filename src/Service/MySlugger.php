<?php

namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;

class MySlugger
{
    
    private $slugger;
    private $toLower;
    private $separator;

    /**
     * Service paramétrable de slug
     *
     * @param SluggerInterface $slugger Service De Symfony
     * @param string $lower argument venant du fichier services.yaml
     * @param string $separator argument venant du fichier services.yaml
     */
    public function __construct(SluggerInterface $slugger,string $lower, string $separator)
    {
        $this->slugger = $slugger;
        // on compare $lower à "true" car on reçoit une chaine de caractère depuis le services.yaml
        $this->toLower = $lower === "true";
        $this->separator = $separator;
        // dd($this);
    }

    public function slug(string $input): string
    {
        // TODO : je dois faire intervenir le service SluggerInterface
        // TODO : Je dois vérifier le paramétrage : separator
        $slug = $this->slugger->slug($input, $this->separator);
        // TODO : Je dois vérifier le paramétrage : lower
        // dump($this->toLower);
        if ($this->toLower || $this->toLower === "true")
        {
            return $slug->lower();
        }
        
        return $slug;
    }
}