<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Drupal\archive\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class YearMonthForm extends FormBase
{
    public function getFormId() { return 'archive_yearmonthform'; }

    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['#method'] = 'get';

        $form['year'] = [
            '#title'         => 'Year',
            '#type'          => 'number',
            '#size'          => 4,
            '#step'          => 1,
            '#default_value' => $form_state->get('year'),
        ];

        $options = [];
        for ($i=1; $i<=12; $i++) { $options[$i] = date('F', mktime(0, 0, 0, $i, 10)); }
        $form['month'] = [
            '#title'         => 'Month',
            '#type'          => 'select',
            '#options'       => $options,
            '#default_value' => $form_state->get('month'),
        ];

        $form['actions'] = [
            '#type'  => 'actions',
            'submit' => [
                '#type'        => 'submit',
                '#value'       => 'Search',
                '#button_type' => 'primary'
            ]
        ];
        return $form;
    }

    public function validateForm(array &$form, FormStateInterface $form_state)
    {
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $form_state->setRebuild();
    }
}
