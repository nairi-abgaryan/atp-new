<?php

namespace App\Controller;

use App\Entity\Donation;
use App\Form\Type\DonationType;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Exception\NotValidCurrentPageException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Session\Session;

class DonationController extends AbstractController
{
    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    /**
     * DonationController constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $session = new Session();
        if($session->isStarted()) {
            $session->start();
        }
    }

    /**
     * @Route("{lang}/donation", name="donation")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws
     */
    public function donation(Request $request)
    {
        $form = $this->createForm(DonationType::class);
        $form->submit($request->request->all());

        if ($form->isSubmitted() && !$form->isValid()) {
            return $this->render('index/donation.html.twig', [
                'form' => $form->createView()
            ]);
        }

        /** @var Donation $data */
        $data = $form->getData();
        if(!$data->getAmount()){
            return $this->render('index/donation.html.twig', [
                'form' => $form->createView(),
                'errorAmount' => true
            ]);
        }

        $data->setCreatedAt(new \DateTime('now'));
        $this->em->persist($data);
        $this->em->flush();

        if($data->getCertificate() == 'Yes'){
            return $this->redirectToRoute('donateReviewCertificate', array('id' => $data->getId()));
        }

        return $this->redirectToRoute('donateReview', array('id' => $data->getId()));

    }

    /**
     * @Route("/adm/donationslist/{page}", name="donationslist")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        $page = $request->query->get('page', 1);

        $session = $this->get('session');

        $donations = $this->getDoctrine()
            ->getRepository(Donation::class)
            ->findBy([], ['id' => 'DESC']);

        $count1 = count($donations);

        $form = $this->createFormBuilder()
            ->add('status', Type\ChoiceType::class, array(
                'choices'  => array('Processed' => 'yes', 'Interrupted' => 'no'), 'required' => false))
            ->add('certificate', Type\ChoiceType::class, array(
                'choices'  => array('Yes' => 'yes', 'No' => 'no'), 'required' => false))
            ->add('name', Type\TextType::class, ['required' => false])
            ->add('email', Type\TextType::class, ['required' => false])
            ->add('transactionId', Type\TextType::class, ['required' => false])
            ->add('send', Type\SubmitType::class, ['label'=>'Apply'])
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            
            $page = 1;

            $session->set('data', $data);
        }
        $data = $session->get('data');

        if($data['status'] == 'yes'){
            foreach ($donations as $key => $value){
                /** @var Donation $value */
                if($value->getTransactionStatus() != '0'){
                    unset($donations[$key]);
                }
            }
        }elseif($data['status'] == 'no'){
            foreach ($donations as $key => $value){
                /** @var Donation $value */
                if($value->getTransactionStatus() == '0'){
                    unset($donations[$key]);
                }
            }
        }

        if($data['certificate'] == 'yes'){
            foreach ($donations as $key=>$value){
                if($value->getCertificate() != 'Yes'){
                    unset($donations[$key]);
                }
            }
        }elseif($data['certificate'] == 'no'){
            foreach ($donations as $key=>$value){
                if($value->getCertificate() == 'Yes'){
                    unset($donations[$key]);
                }
            }
        }

        if($data['name'] != null) {
            foreach ($donations as $key => $value) {
                if ((strpos(strtolower($value->getFirstName()), strtolower($data['name'])) !== false)) {
                    continue;
                }
                if ((strpos(strtolower($value->getLastName()), strtolower($data['name'])) !== false)) {
                    continue;
                }
                unset($donations[$key]);
            }
        }if($data['transactionId'] != null){
            foreach ($donations as $key=>$value){
                if((strpos(strtolower($value->getTransactionId()), strtolower($data['transactionId'])) !== false)){
                    continue;
                }
                unset($donations[$key]);
            }
        }if($data['email'] != null){
            foreach ($donations as $key=>$value){
                if((strpos(strtolower($value->getEmail()), strtolower($data['email'])) !== false)){
                    continue;
                }
                unset($donations[$key]);
            }
        }

        $count2 = count($donations);
        if($count1 == $count2){
            foreach ($donations as $key => $value) {
                if ($value->getTransactionStatus() != '0') {
                    unset($donations[$key]);
                }
            }
        }


        $adapter = new ArrayAdapter($donations);
        $pager =  new Pagerfanta($adapter);
        $pager->setMaxPerPage(20);
        try  {
            $pager->setCurrentPage($page);
        }
        catch(NotValidCurrentPageException $e) {
            throw new NotFoundHttpException('Illegal page');
        }

        return $this->render('donation/index.html.twig', [
            'pager' => $pager,
            'form' => $form->createView()
        ]);
    }
}