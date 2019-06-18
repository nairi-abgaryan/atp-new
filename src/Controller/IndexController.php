<?php

namespace App\Controller;

use App\Manager\FeatureManager;
use Pagerfanta\Exception\NotValidCurrentPageException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\Extension\Core\Type;
use App\Service\Eventbrite;
use App\Service\MailService;
use App\Entity\Donation;
use App\Entity\Events;

/**
 * @Route("/{lang}")
 * @return
 */
class IndexController extends AbstractController
{
    /**
     * @var MailService
     */
    private $mailer;

    /**
     * @var FeatureManager
     */
    private $featureManager;

    /**
     * IndexController constructor.
     * @param MailService $mailer
     * @param FeatureManager $featureManager
     */
    public function __construct(MailService $mailer, FeatureManager $featureManager)
    {
        $this->mailer = $mailer;
        $this->featureManager = $featureManager;
    }

    /**
     * @Route("/index", name="index")
     * @return
     */
    public function index(Request $request)
    {
        $eventbrite = new Eventbrite();
        $response = $eventbrite->getEvents();
        $events = $response['events'];

        if($events != null) {
            for ($i = 0; $i < count($events); $i++) {
                if ($events[$i]['venue_id'] == null) {
                    continue;
                }
                $venue = $eventbrite->getVenue($events[$i]['venue_id']);
                $events[$i]['venue'] = $venue;
            }

            $allEvents = [];
            foreach ($events as $item) {
                $structure = [];
                $structure['name'] = 'event';
                $structure['date'] = date('Y-m-d', strtotime($item['start']['local']));
                $structure['eventTitle'] = $item['name']['text'];
                $structure['eventDescription'] = substr($item['description']['text'], 0, 250) . '...';

                if (isset($item['venue'])) {
                    $structure['eventDestination'] = $item['venue']['address']["localized_area_display"];
                }
                array_push($allEvents, $structure);
            }
        }else{
            $allEvents = null;
        }

        $features = $this->featureManager->findByLinkName($request->attributes->get("_route"));

        return $this->render("index/index.html.twig", [
            'events' => $allEvents,
            'bottom' => $features,
        ]);
    }

    /**
     * @Route("/backyard", name="backyard")
     * @return
     */
    public function backyardNurseries()
    {
        return $this->render('index/backyard-nurseries.html.twig');
    }

    /**
     * @Route("/community", name="community")
     */
    public function community()
    {
        return $this->render('index/community.html.twig');
    }

    /**
     * @Route("/fruit", name="fruit")
     */
    public function fruitHarvesting()
    {
        return $this->render('index/fruit-harvesting.html.twig');
    }

    /**
     * @Route("/impact", name="impact")
     */
    public function impact()
    {
        return $this->render('index/impact.html.twig');
    }

    /**
     * @Route("/economic", name="economic")
     */
    public function economic()
    {
        return $this->render('index/economic.html.twig');
    }

    /**
     * @Route("/tree", name="tree")
     */
    public function tree()
    {
        return $this->render('index/tree.html.twig');
    }

    /**
     * @Route("/forestation", name="forestation")
     */
    public function forestation()
    {
        return $this->render('index/forestation.html.twig');
    }

    /**
     * @Route("/donation", name="donation")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function donation(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('type', Type\HiddenType::class, ['required' => true])
            ->add('amount', Type\HiddenType::class, ['required' => true])
            ->add('firstName', Type\TextType::class, ['required' => true])
            ->add('lastName', Type\TextType::class, ['required' => true])
            ->add('country', Type\HiddenType::class, ['required' => true])
            ->add('city', Type\TextType::class, ['required' => true])
            ->add('state', Type\HiddenType::class, ['required' => true])
            ->add('code', Type\TextType::class, ['required' => true])
            ->add('email', Type\EmailType::class, ['required' => true])
            ->add('address', Type\TextType::class, ['required' => true])
            ->add('phone', Type\TextType::class, ['required' => true])
            ->add('employer', Type\TextType::class, ['required' => false])
            ->add('comments', Type\TextareaType::class, ['required' => false])
            ->add('certificate', Type\ChoiceType::class, array(
                'choices'  => array(
                    'No' => false,
                    'Yes' => true,
                )))
            ->add('send', Type\SubmitType::class, ['label'=>'Next'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if(!isset($data['amount'])){
                return $this->render('index/donation.html.twig', [
                    'form' => $form->createView(),
                    'errorAmount' => true
                ]);
            }
            $entityManager = $this->getDoctrine()->getManager();

            $donation = new Donation();
            $donation->setFirstName($data['firstName']);
            $donation->setLastName($data['lastName']);
            $donation->setAmount($data['amount']);
            $donation->setCountry($data['country']);
            $donation->setCity($data['city']);
            $donation->setState($data['state']);
            $donation->setCode($data['code']);
            $donation->setEmail($data['email']);
            $donation->setAddress($data['address']);
            $donation->setPhone($data['phone']);
            $donation->setEmployer($data['employer']);
            if($data['certificate'] == true){
                $donation->setCertificate('Yes');
            }elseif($data['certificate'] == false){
                $donation->setCertificate('No');
            }
            $donation->setComments($data['comments']);
            $donation->setType($data['type']);
            $donation->setCreatedAt(new \DateTime('now'));

            $entityManager->persist($donation);

            $entityManager->flush();

            if($data['certificate'] == true){
                return $this->redirectToRoute('donateReviewCertificate', array('id' => $donation->getId()));
            }elseif($data['certificate'] == false){
                return $this->redirectToRoute('donateReview', array('id' => $donation->getId()));
            }
        }

        return $this->render('index/donation.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/events", name="events")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function events(Request $request)
    {
        $request = Request::createFromGlobals();
        $page = $request->query->get('page', 1);

        $form = $this->createFormBuilder()
            ->add('date', Type\TextType::class, ['required' => false])
            ->add('title', Type\TextType::class, ['required' => false])
            ->add('location', Type\TextType::class, ['required' => false])
            ->add('send', Type\SubmitType::class, ['label'=>'FIND EVENTS'])
            ->getForm();

        $eventbrite = new Eventbrite();
        $response = $eventbrite->getEvents();
        $events = $response['events'];

        if($events == null){
            return $this->render('index/events.html.twig', [
                'form' => $form->createView(),
                'access' => false
            ]);exit;
        }

        for($i=0; $i<count($events); $i++){
            if($events[$i]['venue_id'] == null){continue;}
            $venue = $eventbrite->getVenue($events[$i]['venue_id']);
            $events[$i]['venue'] = $venue;
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $new_events = [];
            foreach($events as $key => $event){

                $event_time = $event['start']['utc'];
                $date1 = strtotime($event_time);
                if(isset($data['date'])) {
                    $date2 = strtotime(explode(' to ', $data['date'])[0]);
                    $date3 = strtotime(explode(' to ', $data['date'])[1]);
                }

                if(isset($data['title'])){
                    if(strpos(strtolower ($event['name']['text']), strtolower ($data['title']) ) !== false) {
                        if(isset($data['location'])) {
                            if (strpos(strtolower($data['location']), strtolower($event['venue']['address']['city'])) !== false) {
                                if(isset($data['date'])) {
                                    if ($date1 > $date2 && $date1 < $date3)
                                    {
                                        $new_events[]= $event;
                                        continue;
                                    }else{continue;}
                                }else{
                                    $new_events[] = $event;
                                    continue;
                                }
                            }else{
                                continue;
                            }
                        }elseif(isset($data['date'])) {
                            if ($date1 > $date2 && $date1 < $date3)
                            {
                                $new_events[]= $event;
                                continue;
                            }
                        }else{
                            $new_events[]= $event;
                            continue;
                        }
                    }
                    continue;
                }elseif(isset($data['location'])){
                    if (strpos(strtolower($data['location']), strtolower($event['venue']['address']['city'])) !== false) {
                        if (isset($data['date'])) {
                            if ($date1 > $date2 && $date1 < $date3) {
                                $new_events[] = $event;
                                continue;
                            } else {
                                continue;
                            }
                        } else {
                            $new_events[] = $event;
                            continue;
                        }
                    }else{continue;}
                }elseif(isset($data['date'])){
                    if ($date1 > $date2 && $date1 < $date3)
                    {
                        $new_events[]= $event;
                        continue;
                    }
                }else{
                    $new_events[] = $event;
                }
            }

            $events = $new_events;
        }

        $entityManager = $this->getDoctrine()->getManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $localEvents = $queryBuilder->select('e')
            ->from(Events::class, 'e')
            ->orderBy('e.startDate', 'DESC')
            ->getQuery()->getArrayResult();

        $oldEvents = $events;
        foreach($localEvents as $item){
            $new = [];
            $new['name']['text'] = $item['title'];
            $new['description']['text'] = $item['description'];
            $new['start']['local'] = $item['startDate']->format('Y-m-d H:i:s');
            $new['end']['local'] = $item['endDate']->format('Y-m-d H:i:s');
            $new['logo']['url'] = '../uploads/images/'.$item['image'];
            $new['place'] = $item['location'];

            foreach($oldEvents as $elem){
                $str1 = preg_replace('/\s+/', '', $elem['name']['text']);
                $str2 = preg_replace('/\s+/', '', $item['title']);
                if($str1 === $str2){
                    $key = array_search($elem, $oldEvents);
                    unset($events[$key]);
                }
            }
            array_push($events, $new);
        }


        usort($events, function ($item1, $item2) {
            return $item2['start']['local'] <=> $item1['start']['local'];
        });

        $adapter = new ArrayAdapter($events);
        $pager =  new Pagerfanta($adapter);
        $pager->setMaxPerPage(4);
        try  {
            $pager->setCurrentPage($page);
        }
        catch(NotValidCurrentPageException $e) {
            throw new NotFoundHttpException('Illegal page');
        }

        return $this->render('index/events.html.twig', [
            'form' => $form->createView(),
            'events' => $events,
            'pager' => $pager,
            'access' => true,
        ]);
    }

    /**
     * @Route("/where", name="where")
     */
    public function where()
    {
        return $this->render('index/where.html.twig');
    }

    /**
     * @Route("/education", name="education")
     */
    public function education()
    {
        return $this->render('index/education.html.twig');
    }

    /**
     * @Route("/bridges", name="bridges")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function bridges(Request $request)
    {
        $form = $this->createFormBuilder(null, array('csrf_protection' => false))
            ->add('check1', Type\CheckboxType::class, [
                'label'    => 'Teacher Kit',
                'required' => false])
            ->add('check2', Type\CheckboxType::class, [
                'label'    => 'ATP Visit to My School',
                'required' => false])
            ->add('check3', Type\CheckboxType::class, [
                'label'    => 'Bringing My School to Armenia',
                'required' => false])
            ->add('check4', Type\CheckboxType::class, [
                'label'    => 'Volunteering with Building Bridges',
                'required' => false])
            ->add('firstName', Type\TextType::class)
            ->add('lastName', Type\TextType::class)
            ->add('school', Type\TextType::class)
            ->add('title', Type\TextType::class)
            ->add('grade', Type\TextType::class)
            ->add('email', Type\EmailType::class)
            ->add('address', Type\TextType::class, ['required' => false])
            ->add('phone', Type\TextType::class, ['required' => false])
            ->add('comments', Type\TextareaType::class, ['required' => false])
            ->add('send', Type\SubmitType::class, ['label'=>'Submit'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $interest = new Interest();
            $interest->setCheck1($data['check1']);
            $interest->setCheck2($data['check2']);
            $interest->setCheck3($data['check3']);
            $interest->setCheck4($data['check4']);
            $interest->setFirstName($data['firstName']);
            $interest->setLastName($data['lastName']);
            $interest->setSchool($data['school']);
            $interest->setTitle($data['title']);
            $interest->setGrade($data['grade']);
            $interest->setEmail($data['email']);
            $interest->setAddress($data['address']);
            $interest->setPhone($data['phone']);
            $interest->setComments($data['comments']);

            $entityManager->persist($interest);

            $entityManager->flush();

            /** @var  $template */
            $template = $this->render("email/initiative.html.twig", [
                "interest" => $interest
            ]);

            $this->mailer->sendInterestEmail($template, 'sheila@armeniatree.org');

            return $this->render('index/bridges.html.twig', [
                'form' => $form->createView(),
                'done' => true
            ]);

        }

        return $this->render('index/bridges.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/kids", name="kids")
     */
    public function kids()
    {
        return $this->render('index/kids.html.twig');
    }

    /**
     * @Route("/ohanian", name="ohanian")
     */
    public function ohanian()
    {
        return $this->render('index/ohanian.html.twig');
    }

    /**
     * @Route("/volunteer", name="volunteer")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function volunteer(Request $request)
    {
        $form = $this->createFormBuilder(null, array('csrf_protection' => false))
            ->add('check1', Type\CheckboxType::class, [
                'label'    => 'Represent ATP in your community through events and festivals',
                'required' => false])
            ->add('check2', Type\CheckboxType::class, [
                'label'    => 'Volunteer in our Woburn office for an hour or an afternoon',
                'required' => false])
            ->add('check3', Type\CheckboxType::class, [
                'label'    => 'Organize an event for ATP at your church or in your community',
                'required' => false])
            ->add('check4', Type\CheckboxType::class, [
                'label'    => 'Lend a hand at one of our many events throughout the US',
                'required' => false])
            ->add('check5', Type\CheckboxType::class, [
                'label'    => 'Bring ATP’s Building Bridges curriculum to your school or youth group',
                'required' => false])
            ->add('firstName', Type\TextType::class)
            ->add('lastName', Type\TextType::class)
            ->add('city', Type\TextType::class)
            ->add('email', Type\EmailType::class)
            ->add('address', Type\TextType::class)
            ->add('phone', Type\NumberType::class)
            ->add('comments', Type\TextareaType::class, ['required' => false])
            ->add('send', Type\SubmitType::class, ['label'=>'Become Volunteer'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $volunteer = new Volunteer();
            $volunteer->setCheck1($data['check1']);
            $volunteer->setCheck2($data['check2']);
            $volunteer->setCheck3($data['check3']);
            $volunteer->setCheck4($data['check4']);
            $volunteer->setCheck5($data['check5']);
            $volunteer->setFirstName($data['firstName']);
            $volunteer->setLastName($data['lastName']);
            $volunteer->setCity($data['city']);
            $volunteer->setEmail($data['email']);
            $volunteer->setAddress($data['address']);
            $volunteer->setPhone($data['phone']);
            $volunteer->setComments($data['comments']);
            $volunteer->setType('volunteer');

            $entityManager->persist($volunteer);

            $entityManager->flush();

            /** @var  $template */
            $template = $this->render("email/volunteer.html.twig", [
                "volunteer" => $volunteer
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
        $form = $this->createFormBuilder(null, array('csrf_protection' => false))
            ->add('firstName', Type\TextType::class)
            ->add('lastName', Type\TextType::class)
            ->add('city', Type\TextType::class, ['required' => false])
            ->add('email', Type\EmailType::class)
            ->add('address', Type\TextType::class, ['required' => false])
            ->add('phone', Type\NumberType::class, ['required' => false])
            ->add('message', Type\TextareaType::class, ['required' => false])
            ->add('comments', Type\TextareaType::class, ['required' => false])
            ->add('send', Type\SubmitType::class, ['label'=>'Become an Ambassador Today!'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $volunteer = new Volunteer();
            $volunteer->setFirstName($data['firstName']);
            $volunteer->setLastName($data['lastName']);
            $volunteer->setCity($data['city']);
            $volunteer->setEmail($data['email']);
            $volunteer->setAddress($data['address']);
            $volunteer->setPhone($data['phone']);
            $volunteer->setComments($data['comments']);
            $volunteer->setMessage($data['message']);
            $volunteer->setType('ambassador');

            $entityManager->persist($volunteer);

            $entityManager->flush();

            /** @var  $template */
            $template = $this->render("email/ambassador.html.twig", [
                "volunteer" => $volunteer
            ]);

            $this->mailer->sendAmbassadorEmail($template, 'info@armeniatree.org');

            return $this->render('index/ambassador.html.twig', [
                'form' => $form->createView(),
                'done' => true,
            ]);
        }

        return $this->render('index/ambassador.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/empowering", name="empowering")
     */
    public function empowering()
    {
        return $this->render('index/empowering-communities.html.twig');
    }

    /**
     * @Route("/mission", name="mission")
     */
    public function mission()
    {
        return $this->render('index/our-mission.html.twig');
    }

    /**
     * @Route("/team", name="team")
     */
    public function team()
    {
        $branches = $this->getDoctrine()
            ->getRepository(TeamBranches::class)
            ->findAll(['branch_id'=>'DESC']);

        $members = $this->getDoctrine()
            ->getRepository(TeamMember::class)
            ->findAll();

        return $this->render('index/our-team.html.twig', [
            'branches' => $branches,
            'members' => $members
        ]);
    }

    /**
     * @Route("/tour", name="tour")
     */
    public function tour()
    {
        return $this->render('index/tour.html.twig');
    }

    /**
     * @Route("/bndonate", name="bndonate")
     */
    public function bndonate()
    {
        return $this->render('index/bn-donate.html.twig');
    }

    /**
     * @Route("/payment", name="payment")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function payment(Request $request)
    {
        $id = $request->attributes->get('id');

        $donation = $this->getDoctrine()
            ->getRepository(Donation::class)
            ->find($id);

        if($donation->getTransactionStatus() != null){
            $error = $donation->getTransactionStatus();
        }else{
            $error = null;
        }

        if($donation->getType() == 'OneTime'){
            $form = $this->createFormBuilder()
                ->add('accountnumber', Type\TextType::class, ['required' => true])
                ->add('accountholder', Type\TextType::class, ['required' => true])
                ->add('expirymonth', Type\HiddenType::class, ['required' => true])
                ->add('expiryyear', Type\HiddenType::class, ['required' => true])
                ->add('cvv', Type\TextType::class, ['required' => true])
                ->add('send', Type\SubmitType::class, ['label'=>'Donate now'])
                ->getForm();

            $template = 'payment-info-onetime';
        }elseif($donation->getType() == 'Monthly'){
            $form = $this->createFormBuilder()
                ->add('accountnumber', Type\TextType::class, ['required' => true])
                ->add('accountholder', Type\TextType::class, ['required' => true])
                ->add('expirymonth', Type\HiddenType::class, ['required' => true])
                ->add('expiryyear', Type\HiddenType::class, ['required' => true])
                ->add('cvv', Type\TextType::class, ['required' => true])
                ->add('period', Type\ChoiceType::class, array(
                    'choices'  => array(
                        'Weekly' => 'Weekly',
                        'Monthly' => 'Monthly',
                    ),
                    'required' => true))
                ->add('term', Type\NumberType::class, ['required' => true])
                ->add('startMonth', Type\HiddenType::class, ['required' => true])
                ->add('startYear', Type\HiddenType::class, ['required' => true])
                ->add('send', Type\SubmitType::class, ['label'=>'Donate now'])
                ->getForm();

            $template = 'payment-info-monthly';
        }



        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if($data['expirymonth'] == null || $data['expiryyear'] == null ){
                return $this->render('index/'.$template.'.html.twig', [
                    'form' => $form->createView(),
                    'lang' => $this->lang,
                    'errorDate' => true
                ]);
            }

            $matchingPatterns = [
                'visa' => '/^4[0-9]{12}(?:[0-9]{3})?$/',
                'mastercard' => '/^5[1-5][0-9]{5,}|222[1-9][0-9]{3,}|22[3-9][0-9]{4,}|2[3-6][0-9]{5,}|27[01][0-9]{4,}|2720[0-9]{3,}$/',
                'amex' => '/^3[47][0-9]{5,}$/',
                'diners' => '/^3(?:0[0-5]|[68][0-9])[0-9]{11}$/',
                'discover' => '/^6(?:011|5[0-9]{2})[0-9]{12}$/',
                'jcb' => '/^(?:2131|1800|35\d{3})\d{11}$/',
                'any' => '/^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6(?:011|5[0-9][0-9])[0-9]{12}|3[47][0-9]{13}|3(?:0[0-5]|[68][0-9])[0-9]{11}|(?:2131|1800|35\d{3})\d{11})$/'
            ];

            $ctr = 1;
            foreach ($matchingPatterns as $key=>$pattern) {
                if (preg_match($pattern, $data['accountnumber'])) {
                    break;
                }
                $ctr++;
            }

            if($key == 'any'){
                return $this->render('index/'.$template.'.html.twig', [
                    'form' => $form->createView(),
                    'lang' => $this->lang
                ]);
            }

            $entityManager = $this->getDoctrine()->getManager();

            $donation->setAccountNumber($data['accountnumber']);
            $donation->setAccountType($key);
            $donation->setAccountHolder($data['accountholder']);
            $donation->setExpiryMonth($data['expirymonth']);
            $donation->setExpiryYear($data['expiryyear']);
            $donation->setCvv($data['cvv']);
            $donation->setUpdatedAt(new \DateTime('now'));
            if($donation->getType() == 'Monthly'){
                $donation->setPeriod($data['period']);
                $donation->setTerm($data['term']);
                $donation->setStartMonth($data['startMonth']);
                $donation->setStartYear($data['startYear']);
            }

            $entityManager->persist($donation);

            $entityManager->flush();

            if($donation->getType() == 'Monthly'){
                return $this->redirectToRoute('monthlypayflow', array('id' => $donation->getId()));exit;
            }
            return $this->redirectToRoute('singlepayflow', array('id' => $donation->getId()));
        }

        return $this->render('index/'.$template.'.html.twig', [
            'form' => $form->createView(),
            'error' => $error,
            'type' => $donation->getType(),
        ]);
    }

    /**
     * @Route("/donateReview", name="donateReview")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function donationReview(Request $request)
    {
        $id = $request->attributes->get('id');

        $donation = $this->getDoctrine()
            ->getRepository(Donation::class)
            ->find($id);

        $other = $donation->getAmount();
        if($other != 50 && $other != 100 && $other != 200 && $other != 500 && $other != 1000 && $other != 2000 && $other != 5000 && $other != 10000){
            $other = true;
        }else{
            $other = false;
        }


        return $this->render('index/donation-review.html.twig', [
            'donation' => $donation,
            'other' => $other,
        ]);
    }

    /**
     * @Route("/donateReviewCertificate", name="donateReviewCertificate")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function donationReviewCertificate(Request $request)
    {
        $id = $request->attributes->get('id');

        $donation = $this->getDoctrine()
            ->getRepository(Donation::class)
            ->find($id);

        $form = $this->createFormBuilder()
            ->add('name', Type\TextType::class, ['required' => true])
            ->add('nameType', Type\ChoiceType::class, array(
                'choices'  => array(
                    'In honor of' => 'IN HONOR OF',
                    'In memory of' => 'IN MEMORY OF',
                )))
            ->add('fromName', Type\TextType::class, ['required' => true])
            ->add('nameAttention', Type\TextType::class, ['required' => false])
            ->add('address', Type\TextType::class, ['required' => true])
            ->add('country', Type\HiddenType::class, ['required' => true])
            ->add('state', Type\HiddenType::class, ['required' => true])
            ->add('city', Type\TextType::class, ['required' => true])
            ->add('code', Type\NumberType::class, ['required' => true])
            ->add('phone', Type\TextType::class, ['required' => true])
            ->add('comments', Type\TextareaType::class, ['required' => false])
            ->add('send', Type\SubmitType::class, ['label'=>'Next'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $certificate = new Certificate();

            $certificate->setAddress($data['address']);
            $certificate->setName($data['name']);
            $certificate->setNameType($data['nameType']);
            $certificate->setFromName($data['fromName']);
            $certificate->setNameAttention($data['nameAttention']);
            $certificate->setCountry($data['country']);
            $certificate->setState($data['state']);
            $certificate->setPhone($data['phone']);
            $certificate->setCity($data['city']);
            $certificate->setCode($data['code']);
            $certificate->setComments($data['comments']);
            $certificate->setDonationId($id);

            $entityManager->persist($certificate);

            $entityManager->flush();

            return $this->redirectToRoute('certificate', array('id' => $donation->getId()));
        }

        $date = date('d. m. y');

        return $this->render('index/donation-review-certificate.html.twig', [
            'form' => $form->createView(),
            'donation' => $donation,
            'date' => $date,
        ]);
    }

    /**
     * @Route("/donation-certificate", name="certeficate")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function certificate(Request $request)
    {
        $id = $request->attributes->get('id');

        $donation = $this->getDoctrine()
            ->getRepository(Donation::class)
            ->find($id);

        $certificate = $this->getDoctrine()
            ->getRepository(Certificate::class)
            ->findOneBy(['donationId'=>$id]);

        $other = $donation->getAmount();
        if($other != 50 && $other != 100 && $other != 200 && $other != 500 && $other != 1000 && $other != 2000 && $other != 5000 && $other != 10000){
            $other = true;
        }else{
            $other = false;
        }

        return $this->render('index/certificate.html.twig', [
            'donation' => $donation,
            'certificate' => $certificate,
            'other' => $other,
        ]);
    }

    /**
     * @Route("/stewardship", name="stewardship")
     */
    public function stewardship()
    {
        return $this->render('index/stewardship.html.twig');
    }

    /**
     * @Route("/village", name="village")
     */
    public function village()
    {
        return $this->render('index/village.html.twig');
    }

    /**
     * @Route("/environmental-education", name="enveducation")
     */
    public function enveducation()
    {
        return $this->render('index/environmental-education.html.twig');
    }

    /**
     * @Route("/mirak-nursery", name="mirak")
     */
    public function mirak()
    {
        return $this->render('index/mirak-nursery.html.twig');
    }

    /**
     * @Route("/eco-camps", name="camps")
     */
    public function camps()
    {
        return $this->render('index/eco-camp.html.twig');
    }

    /**
     * @Route("/clubs", name="clubs")
     */
    public function clubs()
    {
        return $this->render('index/eco-clubs.html.twig');
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact()
    {

        return $this->render('index/contact.html.twig');
    }

    /**
     * @Route("/photo", name="photo")
     */
    public function photo()
    {
        $photo = $this->getDoctrine()
            ->getRepository(Photo::class)
            ->findAll();

        return $this->render('index/photo-viewer.html.twig', [
            'photo' => $photo
        ]);
    }


    /**
     * @Route("/anniversary", name="anniversary")
     */
    public function anniversary()
    {
        return $this->render('index/anniversary.html.twig');
    }

    /**
     * @Route("/sponsorplanting", name="sponsorplanting")
     */
    public function sponsorplanting()
    {
        return $this->render('index/sponsorplanting.html.twig');
    }

    /**
     * @Route("/forestryfund", name="forestryfund")
     */
    public function forestryfund()
    {
        return $this->render('index/forestryfund.html.twig');
    }

    /**
     * @Route("/sponsorkarinnursery", name="sponsorkarinnursery")
     */
    public function sponsorkarinnursery()
    {
        return $this->render('index/sponsorkarinnursery.html.twig');
    }

    /**
     * @Route("/forestsummit", name="forestsummit")
     */
    public function forestsummit()
    {
        return $this->render('index/forestsummit.html.twig');
    }

    /**
     * @Route("/simpleDonation", name="simpleDonation")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function simpleDonation(Request $request)
    {

        $amount = $request->attributes->get('amount');

        $form = $this->createFormBuilder()
            ->add('type', Type\HiddenType::class, ['required' => true])
            ->add('amount', Type\HiddenType::class, ['required' => true])
            ->add('firstName', Type\TextType::class, ['required' => true])
            ->add('lastName', Type\TextType::class, ['required' => true])
            ->add('country', Type\HiddenType::class, ['required' => true])
            ->add('city', Type\TextType::class, ['required' => true])
            ->add('state', Type\HiddenType::class, ['required' => true])
            ->add('code', Type\TextType::class, ['required' => true])
            ->add('email', Type\EmailType::class, ['required' => true])
            ->add('address', Type\TextType::class, ['required' => true])
            ->add('phone', Type\TextType::class, ['required' => true])
            ->add('employer', Type\TextType::class, ['required' => false])
            ->add('comments', Type\TextareaType::class, ['required' => false])
            ->add('certificate', Type\ChoiceType::class, array(
                'choices'  => array(
                    'No' => false,
                    'Yes' => true,
                )))
            ->add('send', Type\SubmitType::class, ['label'=>'Next'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $donation = new Donation();
            $donation->setFirstName($data['firstName']);
            $donation->setLastName($data['lastName']);
            $donation->setAmount($amount);
            $donation->setCountry($data['country']);
            $donation->setCity($data['city']);
            $donation->setState($data['state']);
            $donation->setCode($data['code']);
            $donation->setEmail($data['email']);
            $donation->setAddress($data['address']);
            $donation->setPhone($data['phone']);
            $donation->setEmployer($data['employer']);
            if($data['certificate'] == true){
                $donation->setCertificate('Yes');
            }elseif($data['certificate'] == false){
                $donation->setCertificate('No');
            }
            $donation->setComments('Sample Description');
            if($amount == 1000){
                $donation->setType('TwoPayments');
            }elseif($amount == 2000){
                $donation->setType('OnePayment');
            }

            $entityManager->persist($donation);

            $entityManager->flush();

            if($data['certificate'] == true){
                return $this->redirectToRoute('certificate', array('id' => $donation->getId()));
            }elseif($data['certificate'] == false){
                return $this->redirectToRoute('donateReview', array('id' => $donation->getId()));
            }
        }

        return $this->render('index/simple_donation.html.twig', [
            'form' => $form->createView(),
            'amount' => $amount,
        ]);
    }

    /**
     * @Route("/gardengala", name="gardengala")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function gardengala(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('type', Type\HiddenType::class, ['required' => true])
            ->add('amount', Type\NumberType::class, ['required' => true])
            ->add('firstName', Type\TextType::class, ['required' => true])
            ->add('lastName', Type\TextType::class, ['required' => true])
            ->add('country', Type\HiddenType::class, ['required' => true])
            ->add('city', Type\TextType::class, ['required' => true])
            ->add('state', Type\HiddenType::class, ['required' => true])
            ->add('code', Type\TextType::class, ['required' => true])
            ->add('email', Type\EmailType::class, ['required' => true])
            ->add('address', Type\TextType::class, ['required' => true])
            ->add('phone', Type\NumberType::class, ['required' => true])
            ->add('employer', Type\TextType::class, ['required' => false])
            ->add('comments', Type\TextareaType::class, ['required' => false])
            ->add('send', Type\SubmitType::class, ['label'=>'Next'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if(!isset($data['amount']) || !isset($data['country']) || !isset($data['state'])){
                return $this->render('index/donation.html.twig', [
                    'form' => $form->createView(),
                    'lang' => $this->lang
                ]);
            }

            $entityManager = $this->getDoctrine()->getManager();

            $donation = new Donation();
            $donation->setFirstName($data['firstName']);
            $donation->setLastName($data['lastName']);
            $donation->setAmount($data['amount']);
            $donation->setCountry($data['country']);
            $donation->setCity($data['city']);
            $donation->setState($data['state']);
            $donation->setCode($data['code']);
            $donation->setEmail($data['email']);
            $donation->setAddress($data['address']);
            $donation->setPhone($data['phone']);
            $donation->setEmployer($data['employer']);
            $donation->setCertificate('No');
            $donation->setComments($data['comments']);
            $donation->setType($data['type']);
            $donation->setCreatedAt(new \DateTime('now'));

            $entityManager->persist($donation);

            $entityManager->flush();

            return $this->redirectToRoute('donateReview', array('id' => $donation->getId()));
        }

        return $this->render('index/gardengala.html.twig', [
            'form' => $form->createView()
        ]);
    }
}