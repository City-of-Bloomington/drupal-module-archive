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
    public function archive($year, $month)
    {
        if (!$year) {
            if (!empty($_GET['year'])) { $year = (int)$_GET['year']; }
            if (!$year) { $year = (int)date('Y'); }
        }
        if (!$month) {
            if (!empty($_GET['month'])) { $month = (int)$_GET['month']; }
            if (!$month) { $month = (int)date('m'); }
        }

        $start = new \DateTime("$year-$month-01");
        $end   = new \DateTime("$year-$month-01");
        $end->add(new \DateInterval('P1M1D'));

        $manager = \Drupal::entityTypeManager();
        $query   = \Drupal::entityQuery('node')
                 ->condition('created', $start->format('U'), '>=')
                 ->condition('created', $end  ->format('U'), '<')
                 ->condition('status', 1);

        $form_state = new FormState();
        $form_state->setAlwaysProcess(true);
        $form_state->setRebuild(true);
        $form_state->set('year',  $year);
        $form_state->set('month', $month);
        $form = \Drupal::formBuilder()->buildForm('Drupal\archive\Form\YearMonthForm', $form_state);

        return [
            '#theme'   => 'archive_results',
            '#year'    => $year,
            '#start'   => $start,
            '#form'    => $form,
            '#results' => $manager->getViewBuilder('node')->viewMultiple(
                              $manager->getStorage('node')->loadMultiple($query->execute()),
                              'teaser'
                          )
        ];
    }
}
