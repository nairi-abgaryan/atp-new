<?php

namespace App\Controller;

use App\Form\Type\AmbassadorType;
use App\Form\Type\VolunteerType;
use App\Manager\AmbassadorManager;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/{lang}")
 * @return
 */
class VolunteerController extends AbstractController
{
    /**
     * @var MailService
     */
    private $mailer;

    /**
     * @var EntityManagerInterface $em
     */
    private $em;
    /**
     * @var AmbassadorManager
     */
    private $ambassadorManager;

    /**
     * DonationController constructor.
     * @param EntityManagerInterface $em
     * @param MailService $mailer
     */
    public function __construct(EntityManagerInterface $em, MailService $mailer, AmbassadorManager $ambassadorManager)
    {
        $this->em = $em;
        $this->mailer = $mailer;
        $this->ambassadorManager = $ambassadorManager;
    }

    /**
     * @Route("/volunteer", name="volunteer")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function volunteer(Request $request)
    {
        $form = $this->createForm(VolunteerType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $data->setType('volunteer');
            $this->em->persist($data);
            $this->em->flush();
            /** @var  $template */
            $template = $this->render("email/volunteer.html.twig", [
                "volunteer" => $data
            ]);

            $this->mailer->sendVolunteerEmail($template, 'info@armeniatree.org');

            return $this->render('index/volunteer.html.twig', [
                'form' => $form->createView(),
                'done' => true
            ]);
        }

        return $this->render('index/volunteer.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/ambassador", name="ambassador")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ambassador(Request $request)
    {
        $form = $this->createForm(AmbassadorType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data =$form->getData();
            $data->setType('ambassador');
            $this->em->persist($data);
            $this->em->flush();

            /** @var  $template */
            $template = $this->render("email/ambassador.html.twig", [
                "volunteer" => $data
            ]);
            $this->mailer->sendAmbassadorEmail($template, 'info@armeniatree.org');



            return $this->render('index/ambassador.html.twig', [
                'form' => $form->createView(),
                'done' => true,
            ]);
        }
        $lang = $request->getLocale();

        $content = $this->ambassadorManager->getAmbassadors($lang);

        return $this->render('index/ambassador.html.twig', [
            'form' => $form->createView(),
            'content' => $content,
        ]);
    }
}
