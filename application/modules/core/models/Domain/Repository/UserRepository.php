<?php

/**
 *  CMF for web applications based on Zend Framework 1 and Doctrine 2
 *  Copyright (C) 2010  Eugene Gruzdev aka yugeon
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright  Copyright (c) 2010 yugeon <yugeon.ru@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.html  GNU GPLv3
 */
/**
 * Description of UserRepository
 *
 * @author yugeon
 */

namespace App\Core\Model\Domain\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{

    /**
     * {@inheritdoc}
     *
     * Overwrite for unit tests.
     *
     * @param EntityManager $em
     * @param ClassMetadata $classMetadata
     */
    public function __construct($em, $classMetadata = null)
    {
        $this->_em = $em;
        if (null !== $classMetadata)
        {
            parent::__construct($em, $classMetadata);
        }
    }

    /**
     * Returns the user with simultaneous preload data needed for auth identity.
     * Minimize the query to the data source.
     *
     * @param array $criteria
     * @return User
     */
    public function getOneForAuthBy(array $criteria)
    {
        $return = null;

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(array('u'))
                ->from($this->getEntityName(), 'u')
                ->leftJoin('u.group', 'g')
                ->leftJoin('g.roles', 'r');

        $count = 0;
        foreach ($criteria as $key => $criterion)
        {
            $count++;
            $qb->andWhere($qb->expr()->eq("u.{$key}", "?{$count}"))
               ->setParameter($count, $criterion);
        }

        $result = $qb->getQuery()->getResult();
        if (!empty($result))
        {
            $return = $result[0];
        }

        return $return;
    }

}
