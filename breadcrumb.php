<?php
/**
 * Created by mjsilva | mail@manueljoaosilva.com
 *
 * Date: 11-10-2011
 * Time: 23:57
 *
 * @USAGE:
 * \Breadcrumb::add_crumb( string [title] , string [link], bool [is home]);
 * +-----------------------------------------------------------------------------------+
 * + Title   | The title of the Crumb it's the anchor text. This got to be unique.     +
 * +-----------------------------------------------------------------------------------+
 * + Link    | The Link of the current page, if not provided we'll use \Uri::current() +
 * +-----------------------------------------------------------------------------------+
 * + is_home | If it's set to true crumbs will be reset and this will be the first.    +
 * +-----------------------------------------------------------------------------------+
 */

class Breadcrumb {

	private static $crumbs = array();

	private static $session_name = "breadcrumb";

	private static $template = array(
		'wrapper_start' => '<ul class="breadcrumb">',
		'wrapper_end' => ' </ul>',
		'crumb_start' => '<li>',
		'crumb_start_active' => '<li class="active">',
		'crumb_end' => '</li>',
		'divider' => '<span class="divider">/</span>'
	);

	/**
	 * Init
	 *
	 * Loads in the config and sets the variables
	 *
	 * @access	public
	 * @return	void
	 */
	public static function _init()
	{
		$config = \Config::get('pagination', array());
		static::set_config($config);
	}

	/**
	 * Set Config
	 *
	 * Sets the configuration for BreadCrumb
	 *
	 * @access public
	 * @param array   $config The configuration array
	 * @return void
	 */
	public static function set_config(array $config)
	{

		foreach ( $config as $key => $value )
		{
			if ( $key == 'template' )
			{
				static::$template = array_merge(static::$template, $config['template']);
				continue;
			}

			static::$
			{$key} = $value;
		}

		static::initialize();
	}

	/**
	 * Adds a new crumb to crumbs static property
	 *
	 * @param string $title Crumb title
	 * @param string $link Relative Crumb link
	 * @return void
	 */
	public static function add_crumb($title, $link = "", $is_home = false)
	{
		// trim the bastard
		$title = trim($title);

		// if link is empty user the current
		$link = (empty($link)) ? \Uri::current() : $link;

		if ( $is_home )
		{
			static::set_home_crumb($title, $link);
			return true;
		}

		// set all crumbs inactive
		// we will re-set this in a sec
		static::set_crumbs_inactive();

		// check if the crumb already exists
		// in the crumbs array
		if ( static::crumb_exists($title) )
		{
			// it exists, lets rewind to it
			static::crumbs_rewind($title);

			// set this crumb active
			static::set_crumb_active($title);
		}
		else
		{
			// it's a new one, lets push it in the array
			array_push(static::$crumbs, array("title" => $title, "link" => $link, "active" => true));
		}

		// save the stuff we did
		static::save_crumbs();
	}

	/**
	 * Tries to set crumbs static property
	 *
	 * @return void
	 */
	private static function initialize()
	{
		if ( !empty(static::$crumbs) ) return true;

		$session_crumbs = static::get_session_crumbs();

		if ( !empty($session_crumbs) )
		{
			static::$crumbs = $session_crumbs;
		}

	}

	/**
	 * Get crumbs from session
	 *
	 * @return array
	 */
	private static function get_session_crumbs()
	{
		return \Session::get(static::$session_name);
	}

	/**
	 * Save crumbs in session
	 *
	 * @return void
	 */
	private static function save_crumbs()
	{
		\Session::set(static::$session_name, static::$crumbs);
	}

	/**
	 *  Set home crumb and destroy the rest
	 *
	 * @param $title
	 * @param $link
	 * @return void
	 */
	private static function set_home_crumb($title, $link)
	{
		static::reset();
		array_push(static::$crumbs, array("title" => $title, "link" => $link, "active" => true));
		// save the stuff we did
		static::save_crumbs();
	}

	/**
	 * Check whether crumb already exists
	 *
	 * @static
	 * @param string $title Crumb title
	 * @return bool
	 */
	private static function crumb_exists($title)
	{
		foreach ( static::$crumbs as $crumb )
		{
			if ( $title === $crumb["title"] ) return true;
		}

		return false;
	}

	/**
	 * Rewinds crumbs array to the specific title
	 *
	 * @static
	 * @param string $title Crumb title
	 * @return bool
	 */
	private static function crumbs_rewind($title)
	{
		// While we still have crumbs remove the last visited
		// until we've reached the one we are currently in
		while ( count(static::$crumbs) > 0 )
		{
			end(static::$crumbs);

			// if we are in the current one stop removing them
			if ( $title === static::$crumbs[key(static::$crumbs)]["title"] ) break;

			// we are still not there continue removing
			unset(static::$crumbs[key(static::$crumbs)]);
		}
	}

	/**
	 * Set all crumbs inactive
	 *
	 * @return void
	 */
	private static function set_crumbs_inactive()
	{
		foreach ( static::$crumbs as $key => $crumb )
		{
			static::$crumbs[$key]["active"] = false;
		}
	}

	/**
	 * Set a specific crumb as active
	 *
	 * @param string $title Crumb title
	 * @return void
	 */
	private static function set_crumb_active($title)
	{
		foreach ( static::$crumbs as $key => $crumb )
		{
			if ( $title === $crumb["title"] )
			{
				static::$crumbs[$key]["active"] = true;
				return true;
			}
		}
	}

	/**
	 * Create Html structure for Crumbs
	 *
	 * @return string The html
	 */
	public static function create_links()
	{
		if ( empty(static::$crumbs) )
		{
			return '';
		}

		$breadcrumb = static::$template["wrapper_start"];
		$breadcrumb .= static::crumb_links();
		$breadcrumb .= static::$template['wrapper_end'];

		return $breadcrumb;
	}

	/**
	 * Loop trough the crumbs and build the links
	 *
	 * @return string
	 */
	private static function crumb_links()
	{
		if ( empty(static::$crumbs) )
		{
			return '';
		}

		$breadcrumb = '';

		foreach ( static::$crumbs as $k => $crumb )
		{
			$is_last = ($k === count(static::$crumbs) - 1);

			$breadcrumb .= ($is_last) ? static::$template["crumb_start_active"] : static::$template["crumb_start"];
			$breadcrumb .= ($is_last) ? $crumb["title"] : \Html::anchor($crumb["link"], $crumb["title"]);
			$breadcrumb .= ($is_last) ? "" : static::$template["divider"];
			$breadcrumb .= static::$template["crumb_end"];
		}

		return $breadcrumb;
	}

	/**
	 * Resets the breadcrumb to empty state
	 *
	 * @return void
	 */
	public static function reset()
	{
		static::$crumbs = array();
		\Session::delete(static::$session_name);
	}
}
