<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\SendMailService;

/**
 * @Route("/contact")
 */
class ContactController extends AbstractController
{
    private $service;

    public function __construct()
    {
        $this->service = new SendMailService();
    }


    /**
     * @Route("/", name="contact_index", methods={"GET"})
     */
    public function index(ContactRepository $contactRepository): Response
    {
        return $this->render('contact/index.html.twig', [
            'contacts' => $contactRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="contact_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($contact);
            $entityManager->flush();

            $nom = $contact->getNom();
            $prenom = $contact->getPrenom();
            $mail = $contact->getMail();
            $message = $contact->getMessage();
            $departmentName = $contact->getDepartment()->getName();
            $mailManager = $contact->getDepartment()->getManagerMail();

            $this->service->sendEmail(
                "$mail",
                "$mailManager",
                "Nouvelle Fiche Contact créé : Département $departmentName",
                "<p>Bonjour,</p>
                <br>
                <p>Une nouvelle fiche contact a été créé pour contacter le manager du département $departmentName. Veuillez trouvez ci-dessous les informations concernant la fiche contact correspondante.</p>
                <br>
                <p>Nom : $nom</p>
                <br> 
                <p>Prénom : $prenom</p>
                <br>
                <p>Email : $mail</p>
                <br>
                <p>Message : $message</p>"
            );

            return $this->redirectToRoute('contact_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('contact/new.html.twig', [
            'contact' => $contact,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="contact_show", methods={"GET"})
     */
    public function show(Contact $contact): Response
    {
        return $this->render('contact/show.html.twig', [
            'contact' => $contact,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="contact_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Contact $contact, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $nom = $contact->getNom();
            $prenom = $contact->getPrenom();
            $mail = $contact->getMail();
            $message = $contact->getMessage();
            $departmentName = $contact->getDepartment()->getName();
            $mailManager = $contact->getDepartment()->getManagerMail();

            $this->service->sendEmail(
                "$mail",
                "$mailManager",
                "Edition de Fiche Contact: Département $departmentName",
                "<p>Bonjour,</p>
                <br>
                <p>Une fiche contact à destination du manager du département $departmentName vient d'être éditée. Veuillez trouvez ci-dessous les informations concernant la fiche contact correspondante.</p>
                <br>
                <p>Nom : $nom</p>
                <br> 
                <p>Prénom : $prenom</p>
                <br>
                <p>Email : $mail</p>
                <br>
                <p>Message : $message</p>"
            );

            return $this->redirectToRoute('contact_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('contact/edit.html.twig', [
            'contact' => $contact,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="contact_delete", methods={"POST"})
     */
    public function delete(Request $request, Contact $contact, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $contact->getId(), $request->request->get('_token'))) {
            $entityManager->remove($contact);
            $entityManager->flush();
        }

        return $this->redirectToRoute('contact_index', [], Response::HTTP_SEE_OTHER);
    }
}
