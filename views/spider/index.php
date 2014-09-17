<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use \denisogr\smartadmin\widgets\JarvisWrapper;
use \denisogr\smartadmin\widgets\Jarvis;

/* @var $model common\modules\ratingkeywords\models\RatingKeywords */
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Spider Report';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rating-keywords-index">
    <h1><?= Html::encode($this->title).': '.$curr_index_name; ?> </h1>
    
    <ul class="nav nav-tabs bordered" id="myTab1">
        <li <? if($curr_index_name == 'Part 1A / page1'):?>class="active"<? endif;?>>
            <?= Html::a('1A / 1', '?r=spider/spider/index&part=1A&spage=1');?>
        </li>
        <li <? if($curr_index_name == 'Part 1A / page2'):?>class="active"<? endif;?>>
            <?= Html::a('1A / 2', '?r=spider/spider/index&part=1A&spage=2');?>
        </li>
        <li <? if($curr_index_name == 'Part 1B / page1'):?>class="active"<? endif;?>>
            <?= Html::a('1B / 1', '?r=spider/spider/index&part=1B&spage=1');?>
        </li>
        <li <? if($curr_index_name == 'Part 1B / page2'):?>class="active"<? endif;?>>
            <?= Html::a('1B / 2', '?r=spider/spider/index&part=1B&spage=2');?>
        </li>
        <li <? if($curr_index_name == 'Part 2 / page1'):?>class="active"<? endif;?>>
            <?= Html::a('2 / 1', '?r=spider/spider/index&part=2&spage=1');?>
        </li>
        <li <? if($curr_index_name == 'Part 2 / page2'):?>class="active"<? endif;?>>
            <?= Html::a('2 / 2', '?r=spider/spider/index&part=2&spage=2');?>
        </li>
        <li <? if($curr_index_name == 'Part 3A / page1'):?>class="active"<? endif;?>>
            <?= Html::a('3A / 1', '?r=spider/spider/index&part=3A&spage=1');?>
        </li>
        <li <? if($curr_index_name == 'Part 3A / page2'):?>class="active"<? endif;?>>
            <?= Html::a('3A / 2', '?r=spider/spider/index&part=3A&spage=2');?>
        </li>
        <li <? if($curr_index_name == 'Part 3B'):?>class="active"<? endif;?>>
            <?= Html::a('3B', '?r=spider/spider/index&part=3B&spage=2');?>
        </li>
        <li <? if($curr_index_name == 'Part 4'):?>class="active"<? endif;?>>
            <?= Html::a('4', '?r=spider/spider/index&part=4&spage=2');?>
        </li>
        <!--<li class="dropdown">
            <a data-toggle="dropdown" class="dropdown-toggle" href="javascript:void(0);">Dropdown <b class="caret"></b></a>
            <ul class="dropdown-menu">
                    <li>
                            <a data-toggle="tab" href="#s3">@fat</a>
                    </li>
                    <li>
                            <a data-toggle="tab" href="#s4">@mdo</a>
                    </li>
            </ul>
        </li>-->
        <li class="pull-right">
            <a href="javascript:void(0);">
            <div data-sparkline-barwidth="7" data-sparkline-width="90px" data-sparkline-height="18px" class="sparkline txt-color-pinkDark text-align-right"><canvas style="display: inline-block; width: 52px; height: 18px; vertical-align: top;" width="52" height="18"></canvas></div> </a>
        </li>
    </ul>
    <section id="widget-grid">
        <div class="row">
            <?php
                JarvisWrapper::begin();
                Jarvis::begin(['wrapperId' => JarvisWrapper::getIdWrapper()]); ?>
                
                <?php
                echo $total_report;
                
                switch ($spage) {
                    case 1: // page 1 (Queries)
                        $columns = [
                            'id',
                            [
                                'attribute' => 'query',
                                'format' => 'raw',
                                'value' => function($model) {
                                    return Html::a($model->query->query, 'https://www.google.com/#q='.urlencode($model->query->query), ['target' => '_blank']);;
                                }
                            ],
                            [
                                'attribute' => 'site',
                                'format' => 'raw',
                                'value' => function($model) {
                                    return Html::a($model->site, 'http://'.$model->site, ['target' => '_blank']);;
                                }
                            ],
                            'create_date',
                            ];
                            break;
                    case 2: // page 2 (PDFs)
                        $columns = [
                            'id',
                            [
                                'label' => 'Query PDF',
                                'attribute' => common\modules\spider\models\SpiderFormsQueriesPdf::tableName().'.query',
                                'format' => 'raw',
                                'value' => function($model) {
                                    return Html::a($model->queryPdf->query, 'https://www.google.com/#q='.urlencode($model->queryPdf->query), ['target' => '_blank']);;
                                }
                            ],
                            (!in_array($part, array('1A','1B','2','3A'))) ?
                                [
                                    'label' => 'Document',
                                    'attribute' => 'site',
                                    'format' => 'raw',
                                    'value' => function($model) {
                                        return $model->queryPdf->site->site;
                                    }
                                ] : 
                                [
                                    'attribute' => 'site',
                                    'format' => 'raw',
                                    'value' => function($model) {
                                        return Html::a($model->queryPdf->site->site, 'http://'.$model->queryPdf->site->site, ['target' => '_blank']);
                                    }
                                ],
                            [
                                'label' => 'Link to PDF',
                                'attribute' => 'link',
                                'format' => 'raw',
                                'value' => function($model) {
                                    return Html::a(
                                        ((strlen(urldecode($model->link)) > 50) ? substr(urldecode($model->link), 0, 50).'...' : urldecode($model->link))
                                        , 'http://'.$model->link, ['target' => '_blank']);;
                                }
                            ],
                            ($part == 4) ? 
                                [
                                    'attribute' => 'doc_site',
                                    'format' => 'raw',
                                    'value' => function($model) {
                                        return $model->queryPdf->site->query->docSiteLink->docSite->doc_site;
                                    }
                                ] : 
                                ['attribute' => 'create_date'],
                            ];
                            break;
                    default:
                        break;
                }
                            
                echo  GridView::widget([
                    'dataProvider' => $model,
//                    'filterModel' => $searchModel,
                    'columns' => $columns,
                    'layout'=>"{items}\n{summary}\n{pager}"
                ]);
                    
                Jarvis::end();
                JarvisWrapper::end();
            ?>
        </div>
    </section>

</div>
