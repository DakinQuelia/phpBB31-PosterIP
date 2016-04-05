<?php
/**
*
* @package phpBB Extension - Poster IP
* @copyright (c) 2015 Dakin Quelia
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace dakinquelia\posterip\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	protected $auth;
	protected $user;
	protected $db;
	protected $phpbb_root_path;
	protected $php_ext;
	
	static public function getSubscribedEvents()
    {
        return array(
            'core.viewtopic_modify_post_row'				=> 'display_posterip_viewtopic',
        );
    }
	
	/**
	* Instead of using "global $user;" in the function, we use dependencies again.
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\auth\auth $auth, \phpbb\user $user, $phpbb_root_path, $php_ext)
	{
		$this->db = $db;
		$this->auth = $auth;
		$this->user = $user;
		$this->root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
	}
    
    public function display_posterip_viewtopic($event)
    {		
	if ($this->auth->acl_get('a_') || $this->auth->acl_get('m_')) {
	    $row = $event['row'];
	    $sql = 'SELECT poster_ip FROM ' . POSTS_TABLE . '
		WHERE post_id = ' . (int) $row['post_id'];
	    $result = $this->db->sql_query($sql);
	    $poster_ip = $this->db->sql_fetchfield('poster_ip');
	    $this->db->sql_freeresult($result);
	    $event['post_row'] = array_merge($event['post_row'], array(
		'POSTER_IP' 		=> $poster_ip,
		'POSTER_IP_VISIBLE' => true,
		'POSTER_IP_WHOIS'	=> "http://www.utrace.de/?query=" . $poster_ip,
	    ));
	}
    }


}