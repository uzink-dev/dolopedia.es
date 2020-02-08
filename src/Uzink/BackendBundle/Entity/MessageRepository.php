<?php
namespace Uzink\BackendBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Uzink\BackendBundle\Search\MessageSearch;
use Uzink\BackendBundle\Entity\User;

class MessageRepository extends EntityRepository {
    /**
     * @param User $user
     * @param string $type
     * @param array $params
     *
     * @return ArrayCollection
     */
    public function searchMessages($user, $type, $params)
    {
        $qb = $this->createQueryBuilder('m');

        $qb->orderBy('m.createdAt', 'DESC');

        switch ($type) {
            case Message::SENT:
                $qb
                    ->where('m.sender = :sender')

                    ->setParameter('sender', $user);
                break;
            case Message::RECEIVED:
                $qb
                    ->leftJoin('m.multipleReceivers', 'mr')
                    ->orWhere('m.receiver = :receiver')
                    ->orWhere($qb->expr()->eq('mr', ':receiver'))
                    ->setParameter('receiver', $user);
                break;
        }

        $selector = array_key_exists(MessageSearch::FILTER_SELECTOR, $params)?$params[MessageSearch::FILTER_SELECTOR]:null;
        $dateFrom = array_key_exists(MessageSearch::FILTER_DATE_FROM, $params)?$params[MessageSearch::FILTER_DATE_FROM]:null;
        $dateTo =  array_key_exists(MessageSearch::FILTER_DATE_TO, $params)?$params[MessageSearch::FILTER_DATE_TO]:null;

        switch ($selector) {
            case 'readed':
                $qb->andWhere('m.readed = true');
                break;
            case 'notReaded':
                $qb->andWhere('m.readed = false');
                break;
            case 'all':
                break;
        }

        if ($dateFrom) {
            $qb->andWhere('m.createdAt >= :date_from')
                ->setParameter(':date_from', $dateFrom);
        }

        if ($dateTo) {
            $dateTo->setTime('23', '59', '59');
            $qb->andWhere('m.createdAt <= :date_to')
                ->setParameter(':date_to', $dateTo);
        }

        return $qb->getQuery()->getResult();
    }

    public function findMultiplePending()
    {
        $qb = $this->createQueryBuilder('m');

        $qb->orderBy('m.createdAt', 'DESC');

        $qb->andWhere('m.multiple = true');
        $qb->andWhere('m.multipleComplete = false');

        return $qb->getQuery()->getResult();
    }
}
