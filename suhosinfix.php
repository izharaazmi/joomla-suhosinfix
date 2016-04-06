<?php
/**
 * @version     1.0.0
 * @package     Suhosinfix
 *
 * @copyright   Copyright (C) 2016. Bhartiy Web Technologies. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE
 * @author      Izhar Aazmi <info@bhartiy.com> - http://www.bhartiy.com
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class plgSystemSuhosinfix
 *
 * Workaround Only! Read below everyday for a healthy website experience!
 * Workaround for the issue caused by some versions of suhosin.
 *
 * Please note that this is just a workaround in case you can't update your suhosin version.
 * It is advised that you disable/uninstall this plugin after your have fixed version of suhosin installed.
 *
 * NOTE: This plugin cannot support the forms submitted with enctype="multipart/formdata" due to the way PHP works.
 *
 * @see  https://github.com/joomla/joomla-cms/issues/8421 and comments.
 */
class plgSystemSuhosinfix extends JPlugin
{
	/**
	 * The global application context
	 *
	 * @var  JApplicationCms
	 */
	protected $app;

	/**
	 * As soon as the Joomla framework is initialized this gets into action.
	 *
	 * @return  void
	 */
	public function onAfterInitialise()
	{
		$raw = plgSystemSuhosinfixWrapper::getValue();

		if (!empty($raw))
		{
			$post = $this->app->input->post->getArray();

			$registry     = new Joomla\Registry\Registry($post);
			$registry_raw = new Joomla\Registry\Registry($raw);

			$registry->merge($registry_raw, true);

			$data = $registry->toArray();

			foreach ($data as $key => $value)
			{
				$this->app->input->post->set($key, $value);
				$this->app->input->set($key, $value);
			}
		}
	}
}

/**
 * Wrapper class to preload required data for this plugin. Otherwise the data would be load forever
 * as 'stdin' can be read only once per session
 */
class plgSystemSuhosinfixWrapper
{
	/**
	 * @var  array
	 */
	protected static $data;

	/**
	 * Return the stored data, if not already stored try to load from 'stdin'
	 *
	 * @return  array
	 */
	public static function getValue()
	{
		if (!isset(static::$data))
		{
			$data = file_get_contents("php://input");

			parse_str($data, static::$data);
		}

		return unserialize(serialize(static::$data));
	}
}

/**
 * Preload the data now
 */
plgSystemSuhosinfixWrapper::getValue();
