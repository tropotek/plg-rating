<?php
namespace Rate\Ui;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class Stars
{

    /**
     * Return HTML that is ready to be formatted by the starRating.css
     *
     * @param float $stars
     * @param bool $showValue
     * @return string
     */
    public static function create($stars = 0.0, $showValue = false)
    {
        $pct = round(($stars/5)*100);
        $value = '';
        if ($showValue)
            $value = sprintf('<div class="value" var="ratingValue">%.2f</div>', (float)$stars);
        return sprintf('<div class="star-rating"><div class="box"><div class="rating" style="width: %s%%;"></div></div>%s</div>', $pct, $value);
    }


}