<?php
/**
 * DokuWiki Plugin whoisonline (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Matthew Mills <millsm@csus.edu>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();


class syntax_plugin_whoisonline extends DokuWiki_Syntax_Plugin {
    /**
     * @return string Syntax mode type
     */
    public function getType() {
        return 'substition';
    }
    /**
     * @return string Paragraph type
     */
    public function getPType() {
        return 'block';
    }
    /**
     * @return int Sort order - Low numbers go before high numbers
     */
    public function getSort() {
        return 155;
    }

	function getonlinelist( $displaymode ){
	global $INFO;
	global $conf;
	
		// read in the file of peoples.
		$filename = DOKU_PLUGIN . 'whoisonline/online.txt';
		if( file_exists( $filename ) == true ) { // read in table
			$online_users = json_decode(file_get_contents( $filename ), true);
		} else { // first time. make dummy array
			$online_users = array();
		}
		
		// add current user to new array
		$displaypattern = $this->getConf('displayline');
		if( isset($INFO['userinfo']) || ($this->getConf('ignoreAnonymous')==0)){ 
			$userdisplay = str_replace( array( 	"{username}" , 
												"{pageid}" ,
												"{fullname}" ,
												"{url}" ) ,
										array(	$INFO["client"] ,
												$INFO["id"] ,
												$INFO["userinfo"]["name"] ,
												$_SERVER["REQUEST_URI"] ) ,
										$displaypattern );
			$newlist = array(array("login"=>$INFO["client"] ,
			"timeseen"=>time() ,
			"display"=>$userdisplay)); // add current user
		} else {
			$newlist = array(); // if user is anonymous and we ignore them make empty new list
		}
		// transfer old list to new list removing old people
		$maxtime = $this->getConf('minutesTillAway')*60;
		if( sizeof($online_users) > 0 ) {
			foreach( $online_users as $user ) {
				if( $user['login']!=$INFO["client"] ) { // skip the already added user
					if( (time() - $user["timeseen"])  < $maxtime ) array_push( $newlist , $user ); 
				}
			}
		}
		$onlinecount = sizeof( $newlist );
		
		// ========= write array back out to file ========= 
		file_put_contents($filename, json_encode($newlist));
		
		$result = "";
		if( $displaymode != "NOSHOW" ) {
			$result .= "<div class='WIO_onlineWidget'>";
			$result .= "<div class='WIO_count'>".$onlinecount."</div>";
			$result .= "<div class='WIO_label'>online</div>";
			if( $displaymode != "NOLIST" ) {
				$result .= "<div class='WIO_panel' style='display:none;'>Loading</div>";
			}
			$result .= "</div>";
		}
		return $result;
	}

    /**
     * Connect lookup pattern to lexer.
     *
     * @param string $mode Parser mode
     */
    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern('~~whoisonline[\#\;\:]?(?:NOLIST|NOSHOW|noshow|nolist)?~~',$mode,'plugin_whoisonline');
    }

    /**
     * Handle matches of the whoisonline syntax
     *
     * @param string $match The match of the syntax
     * @param int    $state The state of the handler
     * @param int    $pos The position in the document
     * @param Doku_Handler    $handler The handler
     * @return array Data for the renderer
     */
    public function handle($match, $state, $pos, &$handler){
        $data = array();
        $data['display'] = strtoupper(substr($match, -8,-2)) ;

        return $data;
    }

    /**
     * Render xhtml output or metadata
     *
     * @param string         $mode      Renderer mode (supported modes: xhtml)
     * @param Doku_Renderer  $renderer  The renderer
     * @param array          $data      The data from the handler() function
     * @return bool If rendering was successful.
     */
    public function render($mode, &$renderer, $data) {
        if($mode != 'xhtml') return false;
		$renderer->doc .= $this->getonlinelist($data['display']);
        return true;
    }
}
