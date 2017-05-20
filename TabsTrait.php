<?php
namespace suver\integrator;

use Yii;
use yii\base\Controller;
use yii\base\Event;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

trait TabsTrait
{
	public static $defaultTab = 'index';
	protected static $activeTab = null;
	protected static $tabs = [];
	protected static $context = null;



	public function addTab($params)
	{
		if (!\Yii::$container->hasSingleton('TabsStorageIntegrator')) {
			\Yii::$container->setSingleton('TabsStorageIntegrator', new StorageIntegrator());
		}
		$tabsStorageIntegrator = \Yii::$container->get('TabsStorageIntegrator');

		$params['tab'] = ArrayHelper::getValue($params, 'tab');
//		if ($tabsStorageIntegrator->has($params['key'])) {
//			throw new \Exception(Yii::t('common', 'Tab {tab} is added. Please chose another name', [
//				'tab' => $params['key']
//			]));
//		}

		$params['name'] = ArrayHelper::getValue($params, 'name');
		$params['window'] = ArrayHelper::getValue($params, 'window');
		$params['module'] = ArrayHelper::getValue($params, 'module');
		$params['controller'] = ArrayHelper::getValue($params, 'controller');
		$params['action'] = ArrayHelper::getValue($params, 'action', null);
		$params['action'] = $params['action'] ?: self::$defaultTab;
		$params['route'] = ArrayHelper::getValue($params, 'route');
		if (is_object($params['controller'])) {
			//$params['controller'] = $params['controller']->controller;
			if (isset($params['controller']->module)) {
				$params['module'] = $params['controller']->module->id;
			}
			$params['controller'] = $params['controller']->id;
		}

		if (empty($params['route'])) {
			$params['route'] = implode("/", [$params['module'], $params['controller'], $params['action']]);
		}
		$params['view'] = ArrayHelper::getValue($params, 'view');
		$params['params'] = ArrayHelper::getValue($params, 'params');
		$params['handler'] = ArrayHelper::getValue($params, 'handler');
		$params['url'] = ArrayHelper::getValue($params, 'url');
		if (empty($params['url'])) {
			$params['url'] = [null, 'tab' => $params['tab']];
		}

		$tabsStorageIntegrator->set('tabs', $params, $params['tab']);
	}

	public function tabs($window)
	{
		if (!\Yii::$container->hasSingleton('TabsStorageIntegrator')) {
			\Yii::$container->setSingleton('TabsStorageIntegrator', new StorageIntegrator());
		}
		$tabsStorageIntegrator = \Yii::$container->get('TabsStorageIntegrator');

		foreach (Yii::$app->modules as $module) {
			if (is_array($module) && isset($module['class'])) {
				$module = new $module['class']('tmp');
			}
			unset($module);
		}


		$moduleId = Yii::$app->controller->module->id;
		$controllerId = Yii::$app->controller->id;
		$actionId = Yii::$app->controller->action->id;

		$tabs = $tabsStorageIntegrator->getCluster('tabs');
		$activeTab = $tabsStorageIntegrator->get('activeTab');

		foreach ($tabs as $tab) {

			List ($_module_Id, $_controllerId, $_actionId) = explode('/', $tab['window']);
			if ($moduleId == $_module_Id && $controllerId == $_controllerId && $actionId == $_actionId) {
				$class = $activeTab == $tab['tab'] ? 'active' : '';
				echo "<li class=\"{$class}\"><a href=\"" . Url::toRoute($tab['url']) . "\">{$tab['name']}</a></li>";
			}
		}
	}

	public function context($tab=null, $event=null)
	{
		if (!\Yii::$container->hasSingleton('TabsStorageIntegrator')) {
			\Yii::$container->setSingleton('TabsStorageIntegrator', new StorageIntegrator());
		}
		$tabsStorageIntegrator = \Yii::$container->get('TabsStorageIntegrator');

		$tab = $tab ?: self::$defaultTab;

		$selectTab = $tabsStorageIntegrator->get('tabs', $tab);
		$activeTab = $tabsStorageIntegrator->get('activeTab');

		if (!$selectTab || !isset ($selectTab['window'])) {
			return null;
		}

		$moduleId = Yii::$app->controller->module->id;
		$controllerId = Yii::$app->controller->id;
		$actionId = Yii::$app->controller->action->id;

		List ($_module_Id, $_controllerId, $_actionId) = explode('/', $selectTab['window']);

		if ($moduleId != $_module_Id || $controllerId != $_controllerId || $actionId != $_actionId) {
			return null;
		}

		foreach (Yii::$app->modules as $module) {
			if (is_array($module) && isset($module['class'])) {
				$module = new $module['class']('tmp');
			}
			unset($module);
		}

		if ($tabsStorageIntegrator->has('tabs', $tab) && $selectTab['tab'] != $activeTab) {

			$tabsStorageIntegrator->set('activeTab', $tab);

			$result = Yii::$app->runAction($selectTab['route'], $selectTab['params']);
			if ($result && is_string($result)) {
				$tabsStorageIntegrator->set('context', $result);
			}
			else {
				return $result;
			}
		}
	}

	public function tabContext()
	{
		if (!\Yii::$container->hasSingleton('TabsStorageIntegrator')) {
			\Yii::$container->setSingleton('TabsStorageIntegrator', new StorageIntegrator());
		}
		$tabsStorageIntegrator = \Yii::$container->get('TabsStorageIntegrator');

		$tab = Yii::$app->request->get('tab');
		$tab = $tab ?: self::$defaultTab;
		if ($tabsStorageIntegrator->has('tabs', $tab)) {
			return $tabsStorageIntegrator->get('context');
		}
		else {
			//throw new NotFoundHttpException();
		}
	}
}