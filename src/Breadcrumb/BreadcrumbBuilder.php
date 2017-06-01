<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Drupal\archive\Breadcrumb;

use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Link;

class BreadcrumbBuilder implements BreadcrumbBuilderInterface
{
    public function applies(RouteMatchInterface $route_match)
    {
        $name = explode('.', $route_match->getRouteName());
        if ($name[0]=='archive' && $name[1]!='settings') { return true; }

        $node = $route_match->getParameter('node');
        if ($node) {
            $config  = \Drupal::config('archive.settings');
            $types   = $config->get('archive_types');
            return (in_array($node->getType(), $config->get('archive_types')));
        }
    }

    public function build(RouteMatchInterface $route_match)
    {
        if (strpos($route_match->getRouteName(), 'archive') === 0) {
            $type  = $route_match->getParameter('type' );
            $year  = $route_match->getParameter('year' );
            $month = $route_match->getParameter('month');
            $day   = $route_match->getParameter('day'  );

            $storage  = \Drupal::entityTypeManager()->getStorage('node_type');
            $nodeType = $storage->load($type);
        }
        else {
            $node = $route_match->getParameter('node');
            if ($node) {
                $type     = $node->getType();
                $created  = $node->getCreatedTime();
                $year     = date('Y', $created);
                $month    = date('m', $created);
                $day      = date('d', $created);

                $nodeType = $node->type->entity;
            }
        }

        $breadcrumb = new Breadcrumb();
        $breadcrumb->addCacheContexts(['url']);
        $breadcrumb->addLink(Link::createFromRoute('Home', '<front>'));
        $breadcrumb->addLink(Link::createFromRoute($nodeType->label(), "archive.$type", ['type'=>$type]));
        if ($year) {
            $breadcrumb->addLink(Link::createFromRoute($year, "archive.$type", ['type'=>$type, 'year'=>$year]));
            if ($month) {
                $monthName = $created ? date('F', $created) : date('F', strtotime("2017-$month-1"));
                $breadcrumb->addLink(Link::createFromRoute($monthName, "archive.$type", ['type'=>$type, 'year'=>$year, 'month'=>$month]));
                if ($day) {
                    $breadcrumb->addLink(Link::createFromRoute($day, "archive.$type", ['type'=>$type, 'year'=>$year, 'month'=>$month, 'day'=>$day]));
                }
            }
        }

        return $breadcrumb;
    }
}
