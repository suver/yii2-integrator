<?php
namespace common\modules\users;

use Yii;
use yii\helpers\ArrayHelper;

class UserProfileIntegration
{
	protected static $tabs = [];

	public function view()
	{

	}

	public static function addTab(UserProfileIInterface $userProfileI)
	{
		self::$tabs[] = $userProfileI;
	}

	public static function tabs()
	{
		foreach (Yii::$app->modules as $module) {
			if (is_array($module) && isset($module['class'])) {
				$module = new $module['class']('tmp');
			}
			unset($module);
		}

		foreach (self::$tabs as $tab) {
			echo "<li class=\"\"><a href=\"" . $tab->getRoute() . "\">" . $tab->getTabName() . "</a></li>";
		}
	}

	public function form()
	{

	}

}