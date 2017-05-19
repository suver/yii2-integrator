<?php
namespace suver\integrator;

use yii\web\Controller;
use yii\base\Event;
use Yii;

/**
 * integrator module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */

    public $menu = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

	    Event::on(Controller::className(), Controller::EVENT_BEFORE_ACTION, function($event) {
		    //var_dump($event);
		    if (method_exists(Yii::$app->controller, 'integrator')) {
			    Yii::$app->controller->integrator($event);
		    }
	    });

	    Event::on(Controller::className(), Controller::EVENT_BEFORE_ACTION, function($event) {
		    //var_dump($event);
		    $tab = Yii::$app->request ? Yii::$app->request->get('tab') : null;
		    Tabs::context($tab, $event);
	    });


	    /*$this->menu = [
			[
				'label' => '<i class="fa fa-home"></i> Загруженые файлы',
				'url' => ['/books'],
				'alias' => ['uploads'],
				'items' => [
					[
						'label' => 'Каталог книг',
						'url' => ['/uploads/list'],
						'alias' => ['uploads/list'],
					],
				],
			],
		];*/

        if (\Yii::$app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'suver\integrator\commands';
        }

        // инициализация модуля с помощью конфигурации, загруженной из config.php
        \Yii::configure($this, require(__DIR__ . '/config.php'));

        // custom initialization code goes here
    }
}
