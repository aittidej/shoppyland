<?php

namespace app\controllers;

use Yii;
use app\models\Brand;
use app\models\OpenOrder;
use app\models\OpenOrderRel;
use app\models\OpenOrderSearch;
use app\models\Product;
use app\models\ProductSearch;
use app\models\UploadFile;
use kartik\file\FileInput;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

use app\components\UpcItemDB;
use app\components\BarcodeLookup;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Product models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Product model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Product model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Product();
		$upload = new UploadFile();
		
        if ($model->load(Yii::$app->request->post()) && $model->save()) 
		{
			$upload->image = UploadedFile::getInstances($upload, "image");
			if(!empty($upload->image))
			{
				$model->image_path = $upload->uploadMultiImages('images/products/' . $model->product_id . '/');
				$model->save(false);
			}
            return $this->redirect(['view', 'id' => $model->product_id]);
        }

		$model->brand_id = 0;
		$model->weight = 0;
		
        return $this->render('create', [
            'model' => $model,
            'upload' => $upload,
        ]);
    }

    /**
     * Updates an existing Product model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->product_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }
	
	public function actionAddProducts($id)
    {
		unset($_GET['id']);
		$model = OpenOrder::findOne($id);
		$products = Product::find()->where(['IN', 'upc', array_values($_GET)])->indexBy('product_id')->orderby('product_id ASC')->all();
		
		foreach ($products as $index => $product) {
			$uploads[$index] = new UploadFile();
		}
		
		if (Product::loadMultiple($products, Yii::$app->request->post()) && Product::validateMultiple($products))
		{
			foreach($uploads AS $product_id=>$upload)
			{
				$upload->image = UploadedFile::getInstances($upload, "[$product_id]image");
				if(!empty($upload->image))
					$imagesPath[$product_id] = $upload->uploadMultiImages('images/products/' . $product_id . '/');
			}
			
            foreach ($products as $product) {
				if(!empty($imagesPath[$product->product_id]))
					$product->image_path = $imagesPath[$product->product_id];
				
                $product->save(false);
            }
			
            return $this->redirect(['/openorder/order/view', 'id' => $id]);
        }
		
		return $this->render('add-products', [
            'model' => $model,
            'products' => $products,
            'uploads' => $uploads,
        ]);
    }

    /**
     * Deletes an existing Product model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
