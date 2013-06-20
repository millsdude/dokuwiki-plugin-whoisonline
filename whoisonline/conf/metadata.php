<?php
/**
 * Options for the whoisonline plugin
 *
 * @author Matthew Mills <millsm@csus.edu>
 */


$meta['minutesTillAway'] = array('numeric','_min'=>'1','_max'=>'60');
$meta['ignoreAnonymous'] = array('onoff');
$meta['displayline'] = array('string');
