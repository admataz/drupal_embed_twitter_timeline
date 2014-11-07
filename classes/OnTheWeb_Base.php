<?php
// Copyright (C) 2014 Adam Davis (adam@admataz.com)

// This program is free software; you can redistribute it and/or modify it under
// the terms of the GNU General Public License as published by the Free Software
// Foundation; either version 2 of the License, or (at your option) any later
// version.

// This program is distributed in the hope that it will be useful, but WITHOUT
// ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
// FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

// You should have received a copy of the GNU General Public License along with
// this program; if not, write to the Free Software Foundation, Inc., 59 Temple
// Place, Suite 330, Boston, MA 02111-1307 USA
// 
// 

/**
 * Base shared methods for OnTheWeb connectors
 */
class OnTheWeb_Base{
// gets rid of those pesky emoji that break the database import
  function remove_non_utf8($text){
    $text = htmlspecialchars_decode(htmlspecialchars($text, ENT_IGNORE, 'UTF-8'));
    // remove non-breaking spaces and other non-standart spaces
    $text = preg_replace('~\s+~u', ' ', $text);
    // replace controls symbols with "?"
    $text = preg_replace('~\p{C}+~u', '-', $text);

    return $text;
  }
  


/**
 * slight variation on http://stackoverflow.com/a/501415 - changed DEFINE to local variables
 */
static function relativeTime($time)
{   

    $SECOND =  1;
    $MINUTE =  60 * $SECOND;
    $HOUR =  60 * $MINUTE;
    $DAY =  24 * $HOUR;
    $MONTH =  30 * $DAY;

    $time = strtoTime($time);

    $delta = time() - $time;

    if ($delta < 1 * $MINUTE)
    {
        return $delta == 1 ? "one second ago" : $delta . " seconds ago";
    }
    if ($delta < 2 * $MINUTE)
    {
      return "a minute ago";
    }
    if ($delta < 45 * $MINUTE)
    {
        return floor($delta / $MINUTE) . " minutes ago";
    }
    if ($delta < 90 * $MINUTE)
    {
      return "an hour ago";
    }
    if ($delta < 24 * $HOUR)
    {
      return floor($delta / $HOUR) . " hours ago";
    }
    if ($delta < 48 * $HOUR)
    {
      return "yesterday";
    }
    if ($delta < 30 * $DAY)
    {
        return floor($delta / $DAY) . " days ago";
    }
    if ($delta < 12 * $MONTH)
    {
      $months = floor($delta / $DAY / 30);
      return $months <= 1 ? "one month ago" : $months . " months ago";
    }
    else
    {
        $years = floor($delta / $DAY / 365);
        return $years <= 1 ? "one year ago" : $years . " years ago";
    }
}   
}
