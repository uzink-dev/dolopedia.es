<?php

namespace Uzink\BackendBundle\Form\ChoiceList;

use Symfony\Component\Form\Extension\Core\ChoiceList\SimpleChoiceList;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;

use Uzink\BackendBundle\Entity\Article;

class ArticleTypesChoiceList extends SimpleChoiceList {
    public function __construct() {
        $choices = array(
            Article::TYPE_TECHNIQUES    => 'article.types.techniques.title',
            Article::TYPE_CLINIC_ENTITY => 'article.types.clinicEntity.title',
            Article::TYPE_GENERAL       => 'article.types.general.title',
            Article::TYPE_DRUG          => 'article.types.drug.title',
        );
        
        parent::__construct($choices, array());
    }
    
    protected function createValue($choice) {
        return $choice;
    }
}

?>
