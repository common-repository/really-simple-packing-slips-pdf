<?php
/**
 * @package dompdf
 * @link    http://dompdf.github.com/
 * @author  Benj Carson <benjcarson@digitaljunkies.ca>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */

namespace Dompdf\Positioner;

use Dompdf\FrameDecorator\AbstractFrameDecorator;

/**
 * Positions block frames
 *
 * @access  private
 * @package dompdf
 */
class Block extends AbstractPositioner {

    function position(AbstractFrameDecorator $frame)
    {
        $style = $frame->get_style();
        $cb = $frame->get_containing_block();
        $p = $frame->find_block_parent();

        if ($p) {
            $float = $style->float;

            if (!$float || $float === "none") {
                $p->add_line(true);
            }
            $y = $p->get_current_line_box()->y;

        } else {
            $y = $cb["y"];
        }

        $x = $cb["x"];

        // Relative positionning
        if ($style->position === "relative") {
            $top = (float)$style->length_in_pt($style->top, $cb["h"]);
            //$right =  (float)$style->length_in_pt($style->right,  $cb["w"]);
            //$bottom = (float)$style->length_in_pt($style->bottom, $cb["h"]);
            $left = (float)$style->length_in_pt($style->left, $cb["w"]);

            $x += $left;
            $y += $top;
        }

        $frame->set_position($x, $y);
    }
}
