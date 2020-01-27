<?php

namespace App\Service;

use Twig\Environment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PaginationService{

    private $entityClass; // l'netité sur laquelle on doit faire la pagination
    private $limit = 10;
    private $currentPage = 1;
    private $manager;
    private $twig;
    private $route;
    private $templatePath;


    public function __construct(EntityManagerInterface $manager, Environment $twig, RequestStack $request, $templatePath){
        $this->manager = $manager;
        $this->twig = $twig;
        $this->route = $request->getCurrentRequest()->attributes->get('_route'); // pour rendre la route automatique, pas besoin de la set
        $this->templatePath = $templatePath;
    }

    public function display(){
        $this->twig->display($this->templatePath,[
            'page' => $this->currentPage,
            'pages' => $this->getPages(),
            'route' => $this->route
        ]);
    }

    public function setRoute($route){
        $this->route = $route;
        return $this;
    }

    public function getRoute(){
        return $this->route;
    }

    public function setTemplatePath($templatePath){
        $this->templatePath = $templatePath;
        return $this;
    }

    public function getTemplatePath(){
        return $this->templatePath;
    }

    public function setPage($page){
        $this->currentPage = $page;
        return $this;
    }

    public function getPage(){
        return $this->currentPage;
    }

    public function setLimit($limit){
        $this->limit = $limit;
        return $this;
    }

    public function getLimit(){
        return $this->limit;
    }

    public function setEntityClass($entityClass){
        $this->entityClass = $entityClass;
        return $this;
    }

    public function getEntityClass(){
        return $this->entityClass;
    }

    public function getData(){
        // Calculer l'offset
        $offset = $this->currentPage * $this->limit - $this->limit;
        // demander au repository de trouver les éléments 
        $repo = $this->manager->getRepository($this->entityClass);
        $data = $repo->findBy([],[], $this->limit, $offset);

        //renvoyer les éléments en question 
        return $data;
    }

    public function getPages(){
        // connaitre le total des enregistrements de la table
        $repo = $this->manager->getRepository($this->entityClass);
        $total =  count($repo->findAll());
        // faire la division, l'arrondi et le renvoyer
        $pages = ceil($total / $this->limit);

        return $pages;
    }


}