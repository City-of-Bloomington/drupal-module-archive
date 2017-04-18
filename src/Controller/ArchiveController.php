<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Drupal\archive\Controller;

use Drupal\Core\Controller\ControllerBase;

class ArchiveController extends ControllerBase
{
    public function archive($year, $month)
    {
        $year  = $year  ? (int)$year  : (int)date('Y');
        $month = $month ? (int)$month : (int)date('m');

        $start = new \DateTime("$year-$month-01");
        $end   = new \DateTime("$year-$month-01");
        $end->add(new \DateInterval('P1M1D'));

        $manager = \Drupal::entityTypeManager();
        $query   = \Drupal::entityQuery('node')
                 ->condition('created', $start->format('U'), '>=')
                 ->condition('created', $end  ->format('U'), '<=')
                 ->condition('status', 1);

        return [
            '#theme'   => 'archive_results',
            '#results' => $manager->getViewBuilder('node')->viewMultiple(
                              $manager->getStorage('node')->loadMultiple($query->execute()),
                              'teaser'
                          )
        ];
    }
}
