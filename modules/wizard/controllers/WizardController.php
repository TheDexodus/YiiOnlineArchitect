<?php

declare(strict_types=1);

namespace wizard\controllers;

use app\models\Material;
use app\models\MaterialType;
use wizard\models\forms\WizardForm;
use Yii;
use yii\web\Controller;

/**
 * Class WizardController
 */
class WizardController extends Controller
{
    /**
     * @param int $step
     *
     * @return string
     */
    public function actionIndex(int $step = 0): string
    {
        $form = new WizardForm(['scenario' => WizardForm::SCENARIO_SET_ROOM_SETTINGS]);

        if ($form->load(Yii::$app->request->post()) && $form->validate() && $step > 1) {
            $form->setScenario(WizardForm::SCENARIO_SET_OPENINGS);

            if ($form->load(Yii::$app->request->post()) && $form->validate() && $step > 2) {
                $form->setScenario(WizardForm::SCENARIO_SELECT_MATERIAL);

                /** @var Material[] $allMaterials */
                $allMaterials = Material::find()->all();
                $materials = [];

                $materials[MaterialType::USAGE_FLOORS] = [];
                $materials[MaterialType::USAGE_WALLS] = [];
                $materials[MaterialType::USAGE_CELLS] = [];

                foreach ($allMaterials as $material) {
                    switch ($material->type->usage) {
                        case MaterialType::USAGE_FLOORS:
                            $materials[MaterialType::USAGE_FLOORS][$material->vendor_code] = $material;
                            break;
                        case MaterialType::USAGE_WALLS:
                            $materials[MaterialType::USAGE_WALLS][$material->vendor_code] = $material;
                            break;
                        case MaterialType::USAGE_CELLS:
                            $materials[MaterialType::USAGE_CELLS][$material->vendor_code] = $material;
                            break;
                    }
                }

                $form->goodMaterials = $materials;

                if ($form->load(Yii::$app->request->post()) && $form->validate() && $step > 3) {
                    $form->setScenario(WizardForm::SCENARIO_CONFIRM);

                    $materialRecords = [];

                    foreach ($form->materials as $type => $material) {
                        $materialRecords[$type] = $materials[$type][$material];
                    }

                    $form->goodMaterials = $materialRecords;

                    if ($step > 4) {
                        return $this->render(
                            'bill',
                            [
                                'form' => $form,
                            ]
                        );
                    }

                    return $this->render(
                        'visualization',
                        [
                            'form'    => $form,
                            'records' => $materialRecords,
                        ]
                    );
                }

                return $this->render(
                    'select_material',
                    [
                        'form'      => $form,
                        'materials' => $materials,
                    ]
                );
            }

            return $this->render(
                'set_openings',
                [
                    'form' => $form,
                ]
            );
        }

        return $this->render(
            'set_room_settings',
            [
                'form' => $form,
            ]
        );
    }
}
