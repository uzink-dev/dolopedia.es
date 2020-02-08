<?php

namespace Uzink\BackendBundle\Search;

use Symfony\Component\HttpFoundation\Request;
use Uzink\BackendBundle\Entity\Message;

class MessageSearch extends Search
{
    const TAB_SENT = Message::SENT;
    const TAB_RECEIVED = Message::RECEIVED;

    const FILTER_DATE_FROM = 'dateFrom';
    const FILTER_DATE_TO = 'dateTo';

    protected $tabs = array(
        self::TAB_RECEIVED,
        self::TAB_SENT
    );

    /**
     * @param Request $request
     * @return void
     */
    public function handleRequest(Request $request)
    {
        $methodSkeleton = 'handleRequestFromTab';
        foreach ($this->tabs as $tab) {
            $method = $methodSkeleton.ucfirst($tab);
            if ($this->hasMethod($method, get_class())) {
                $this->$method($request);
            }
        }
    }

    /**
     * @param Request $request
     */
    public function handleRequestFromTabReceived(Request $request)
    {
        $messageManager = $this->container->get('uzink.message.manager');

        $form = $this->getForm(self::TAB_RECEIVED);
        $form->handleRequest($request);

        $this->setPage(self::TAB_RECEIVED, $request->get(self::TAB_RECEIVED, 1));

        $prefix = self::TAB_RECEIVED;

        $selector = $form->get($prefix . ucfirst(self::FILTER_SELECTOR))->getData();
        $dateFrom = $form->get($prefix . ucfirst(self::FILTER_DATE_FROM))->getData();
        $dateTo = $form->get($prefix . ucfirst(self::FILTER_DATE_TO))->getData();

        $params= array();
        $params[self::FILTER_SELECTOR] = $selector;
        $params[self::FILTER_DATE_FROM] = $dateFrom;
        $params[self::FILTER_DATE_TO] = $dateTo;

        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();

        $this->entities[self::TAB_RECEIVED] = $messageManager->getRepository()->searchMessages($user, self::TAB_RECEIVED, $params);
    }

    /**
     * @param Request $request
     */
    public function handleRequestFromTabSent(Request $request)
    {
        $messageManager = $this->container->get('uzink.message.manager');

        $form = $this->getForm(self::TAB_SENT);
        $form->handleRequest($request);

        $this->setPage(self::TAB_SENT, $request->get(self::TAB_SENT, 1));

        $prefix = self::TAB_SENT;
        $dateFrom = $form->get($prefix . ucfirst(self::FILTER_DATE_FROM))->getData();
        $dateTo = $form->get($prefix . ucfirst(self::FILTER_DATE_TO))->getData();

        $params= array();
        $params[self::FILTER_DATE_FROM] = $dateFrom;
        $params[self::FILTER_DATE_TO] = $dateTo;

        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();

        $this->entities[self::TAB_SENT] = $messageManager->getRepository()->searchMessages($user, self::TAB_SENT, $params);
    }
}