<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class PaginationService{

    private $entityClass; // l'netité sur laquelle on doit faire la pagination
    private $limit = 10;
    private $currentPage = 1;
    private $manager;


    public function __construct(EntityManagerInterface $manager){
        $this->manager = $manager;
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