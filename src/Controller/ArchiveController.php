<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Drupal\archive\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormState;

class ArchiveController extends ControllerBase
{
    public function archive($type, $year, $month, $day)
    {
        $year  = (int)$year;
        $month = (int)$month;
        $day   = (int)$day;

        if (!$year) { $year = (int)date('Y'); }

        if (!$month) {
            $date = "$year-01-01";
            $period = new \DateInterval('P1Y1D');
        }
        else {
            if (!$day) {
                $date   = "$year-$month-01";
                $period = new \DateInterval('P1M1D');
            }
            else {
                $date   = "$year-$month-$day";
                $period = new \DateInterval('P1D');
            }
        }
        $start = new \DateTime($date);
        $end   = new \DateTime($date);
        $end->add($period);

        $manager = \Drupal::entityTypeManager();
        $query   = \Drupal::entityQuery('node')
                 ->condition('type',    'news_release')
                 ->condition('created', $start->format('U'), '>=')
                 ->condition('created', $end  ->format('U'), '<')
                 ->condition('status',  1);

        #$form_state = new FormState();
        #$form_state->setAlwaysProcess(true);
        #$form_state->setRebuild(true);
        #$form_state->set('year',  $year);
        #$form_state->set('month', $month);
        #$form = \Drupal::formBuilder()->buildForm('Drupal\archive\Form\YearMonthForm', $form_state);

        return [
            '#theme'   => 'archive_results',
            '#year'    => $year,
            '#start'   => $start,
            '#form'    => null,
            '#results' => $manager->getViewBuilder('node')->viewMultiple(
                              $manager->getStorage('node')->loadMultiple($query->execute()),
                              'teaser'
                          )
        ];
    }
}
