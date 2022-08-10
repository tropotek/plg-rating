<?php
namespace Rate\Ui;


/**
 * @author Michael Mifsud <http://www.tropotek.com/>
 * @see http://www.tropotek.com/
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
        // TODO: Make this a config option in the plugin settings????
        $dec = 0;

        $stars = round($stars, $dec);
        $pct = round(($stars/5)*100);
        $value = '';
        if ($showValue)
            $value = sprintf('<div class="value" var="ratingValue">%s</div>', $stars);
        return sprintf('<div class="star-rating"><div class="box"><div class="rating" style="width: %s%%;"></div></div>%s</div>', $pct, $value);
    }


}