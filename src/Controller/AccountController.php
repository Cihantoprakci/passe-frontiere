<?php

namespace App\Controller;

use App\Entity\Alert;
use App\Entity\Commute;
use App\Entity\User;
use App\Form\AlertType;
use App\Form\CommuteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/mon-compte')]
class AccountController extends AbstractController
{
    #[Route('', name: 'account')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getCurrentUser();

        $commute = $user->getCommute();
        if (!$commute) {
            $commute = new Commute();
            $user->setCommute($commute);
        }

        $commuteForm = $this->createForm(CommuteType::class, $commute);
        $commuteForm->handleRequest($request);

        if ($commuteForm->isSubmitted() && $commuteForm->isValid()) {
            $entityManager->persist($commute);
            $entityManager->flush();

            $this->addFlash('success', 'Votre trajet est enregistré.');

            return $this->redirectToRoute('account');
        }

        $alert = new Alert();
        if ($commute->getBorder()) {
            $alert->setBorder($commute->getBorder());
        }

        $alertForm = $this->createForm(AlertType::class, $alert);
        $alertForm->handleRequest($request);

        if ($alertForm->isSubmitted() && $alertForm->isValid()) {
            if ($alert->getWindowEnd() <= $alert->getWindowStart()) {
                $this->addFlash('error', 'L\'heure de fin doit être après l\'heure de début.');
            } else {
                $user->addAlert($alert);
                $entityManager->persist($alert);
                $entityManager->flush();

                $this->addFlash('success', 'L\'alerte est ajoutée.');

                return $this->redirectToRoute('account');
            }
        }

        return $this->render('account/index.html.twig', [
            'user' => $user,
            'commuteForm' => $commuteForm,
            'alertForm' => $alertForm,
        ]);
    }

    #[Route('/alertes/{id}/activer', name: 'account_alert_toggle', methods: ['POST'])]
    public function toggleAlert(Alert $alert, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->checkAlert($alert, $request, 'toggle');

        $alert->setActive(!$alert->isActive());
        $entityManager->flush();

        $this->addFlash('success', $alert->isActive() ? 'L\'alerte est activée.' : 'L\'alerte est en pause.');

        return $this->redirectToRoute('account');
    }

    #[Route('/alertes/{id}/supprimer', name: 'account_alert_delete', methods: ['POST'])]
    public function deleteAlert(Alert $alert, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->checkAlert($alert, $request, 'delete');

        $entityManager->remove($alert);
        $entityManager->flush();

        $this->addFlash('success', 'L\'alerte est supprimée.');

        return $this->redirectToRoute('account');
    }

    #[Route('/consentement', name: 'account_consent', methods: ['POST'])]
    public function consent(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('consent', $request->getPayload()->getString('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->getCurrentUser();
        $user->setAlertsConsent(!$user->hasAlertsConsent());
        $entityManager->flush();

        $this->addFlash('success', $user->hasAlertsConsent() ? 'Vous recevrez les alertes par e-mail.' : 'Vous ne recevrez plus d\'e-mails.');

        return $this->redirectToRoute('account');
    }

    #[Route('/supprimer', name: 'account_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        if (!$this->isCsrfTokenValid('delete-account', $request->getPayload()->getString('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->getCurrentUser();
        $security->logout(false);

        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', 'Votre compte et vos données sont supprimés.');

        return $this->redirectToRoute('dashboard');
    }

    private function getCurrentUser(): User
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        return $user;
    }

    private function checkAlert(Alert $alert, Request $request, string $action): void
    {
        if ($alert->getUser() !== $this->getCurrentUser()) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->isCsrfTokenValid($action . '-alert-' . $alert->getId(), $request->getPayload()->getString('_token'))) {
            throw $this->createAccessDeniedException();
        }
    }
}
