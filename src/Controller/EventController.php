<?php

namespace App\Controller;

use App\Entity\Events;
use App\Form\Type\FindEventType;
use App\Service\Eventbrite;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Exception\NotValidCurrentPageException;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/{lang}")
 * @return
 */
class EventController extends AbstractController
{
    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    /**
     * EventController constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/events", name="events")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function events(Request $request)
    {
        $lang = ucfirst($request->getLocale());
        var_dump($lang);die;
        $page = $request->query->get('page', 1);
        $form = $this->createForm(FindEventType::class);

        $form->handleRequest($request);

        $eventbrite = new Eventbrite();
        $response = $eventbrite->getEvents();
        $events = $response['events'];

        if($events == null){
            return $this->render('index/events.html.twig', [
                'form' => $form->createView(),
                'access' => false
            ]);
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
                }else{
                    return $this->render('index/events.html.twig', [
                        'form' => $form->createView(),
                        'access' => false
                    ]);
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

        $queryBuilder = $this->em->createQueryBuilder();

        $localEvents = $queryBuilder->select('e')
            ->from(Events::class, 'e')
            ->orderBy('e.startDate', 'DESC')
            ->getQuery()->getArrayResult();

        $oldEvents = $events;
        foreach($localEvents as $item){
            $new = [];
            $new['name']['text'] = $item['title'];
            $new['description']['text'] = $item['description'];
            /** @var \DateTime $startDate */
            $startDate = $item['startDate'];
            /** @var \DateTime $endDate */
            $endDate = $item['endDate'];
            $new['start']['local'] = $startDate->format('Y-m-d H:i:s');
            $new['end']['local'] = $endDate->format('Y-m-d H:i:s');
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
}