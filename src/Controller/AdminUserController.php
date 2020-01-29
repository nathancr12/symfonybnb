<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AdminUserType;
use App\Repository\RoleRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminUserController extends AbstractController
{
    /**
     * @Route("/admin/user/{page<\d+>?1}", name="admin_user_index")
     */
    public function index($page, PaginationService $pagination)
    {
        $pagination->setEntityClass(User::class)
        ->setPage($page)
        ->setLimit(10)
        ;

        return $this->render('admin/user/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/admin/users/{id}/edit", name="admin_user_edit")
     *
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(User $user, Request $request, EntityManagerInterface $manager){
        $form = $this->createForm(AdminUserType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'utilisateur n°<strong>{$user->getId()}</strong> a été modifié"
            );
        }

        return $this->render('admin/user/edit.html.twig',[
            'user' => $user,
            'myForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/users/{id}/delete", name="admin_user_delete")
     *
     * @param User $user
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(User $user, EntityManagerInterface $manager){
        if(count($user->getAds()) > 0){
            $this->addFlash(
                'warning',
                "Vous ne pouvez pas supprimer l'utilisateur <strong>{$user->getId()}</strong> car il possède déjà des annonces"
            );
        
        }elseif(count($user->getBookings()) > 0){
            $this->addFlash(
                'warning',
                "Vous ne pouvez pas supprimer l'utilisateur <strong>{$user->getId()}</strong> car il possède déjà des réservations"
            );
        }else{
            if(!empty($user->getPicture())){
                unlink($this->getParameter('uploads_directory'.'/'.$user->getPicture()));
            }

            $manager->remove($user);
            $this->addFlash(
                'success',
                'L\'utilisateur <strong>$user->getFullName</strong> a bien été supprime'
            );
        }

        return $this->redirectToRoute('admin_user_index');
    }

    /**
     * @Route("/admin/users/{id}/addAdmin", name="admin_users_addAdmin")
     *
     * @param User $user
     * @param EntityManagerInterface $manager
     * @param RoleRepository $repo
     * @return Response
     */
    public function addAdmin(User $user, EntityManagerInterface $manager, RoleRepository $role){
        $user->addUserRole($role->findOneByTitle('ROLE_ADMIN'));
    
        $manager->persist($user);
        $manager->flush();
    
        $this->addFlash(
            'success',
            "Vous avez nommé un nouvel administrateur"
        );
    
         return $this->redirectToRoute('admin_user_edit', ['id' => $user->getId()]);
    }

    /**
     * @Route("/admin/users/{id}/removeAdmin", name="admin_users_removeAdmin")
     *
     * @param User $user
     * @param EntityManagerInterface $manager
     * @param RoleRepository $repo
     * @return Response
     */
    public function removeAdmin(User $user, EntityManagerInterface $manager, RoleRepository $role){
        $user->removeUserRole($role->findOneByTitle('ROLE_ADMIN'));
    
        $manager->persist($user);
        $manager->flush();
    
        $this->addFlash(
            'warning',
            "Vous avez supprimé un administrateur"
        );
    
         return $this->redirectToRoute('admin_user_edit', ['id' => $user->getId()]);
    }

}

