<?php

declare(strict_types=1);

namespace importer\models\forms;

use app\models\Material;
use app\models\MaterialType;
use Dotenv\Exception\InvalidFileException;
use Exception;
use importer\components\helpers\FileHelper;
use importer\components\parsers\ParserFactory;
use Throwable;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use ZipArchive;

/**
 * Class ImportForm
 */
class ImportForm extends Model
{
    /** @var UploadedFile */
    public $file;

    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return [
            ['file', 'required'],
            ['file', 'file', 'skipOnEmpty' => false, 'extensions' => ['zip']],
        ];
    }

    /**
     * @return array|null
     *
     * @throws Throwable
     */
    public function importModels(): ?array
    {
        if (!$this->validate()) {
            return null;
        }

        $zip = new ZipArchive();
        $result = [];

        if ($zip->open($this->file->tempName) === true) {
            $parts = explode('/', $this->file->tempName);
            $extractDirPath = Yii::getAlias('@web/app/tmp/'.$parts[count($parts) - 1]).'/';

            $zip->extractTo($extractDirPath);
            $zip->close();

            try {
                $canExtensions = ['txt', 'csv', 'xls'];

                $materialExtension = null;
                $materialTypeExtension = null;
                foreach ($canExtensions as $extension) {
                    if (file_exists($extractDirPath.'materials.'.$extension)) {
                        $materialExtension = $extension;
                    }

                    if (file_exists($extractDirPath.'material_types.'.$extension)) {
                        $materialTypeExtension = $extension;
                    }
                }

                if ($materialExtension === null ||
                    $materialTypeExtension === null ||
                    !file_exists($extractDirPath.'images') ||
                    !is_dir($extractDirPath.'images')
                ) {
                    $this->addError(
                        'file',
                        'Invalid files: In the zip archive there should be a "images" directory and 2 files (in one of the following expansions: txt, csv, xsl): materials, material_types'
                    );

                    return null;
                }
                try {
                    $materialTypeParser = ParserFactory::getParser((string) $materialTypeExtension);
                    if (($result['material_types'] = $materialTypeParser->parse(
                            $extractDirPath.'material_types.'.$materialTypeExtension,
                            MaterialType::class
                        )) === null) {
                        return null;
                    }

                    $materialParser = ParserFactory::getParser((string) $materialExtension);
                    if (($materials = $materialParser->parse(
                            $extractDirPath.'materials.'.$materialTypeExtension,
                            Material::class
                        )) === null) {
                        return null;
                    }

                    $result['materials'] = [];
                    /** @var Material $material */
                    foreach ($materials as $material) {
                        if ($material->use_pattern === 'picture') {
                            $picture = $material->picture;

                            if (!file_exists($extractDirPath.'images/'.$picture)) {
                                $material->delete();
                                continue;
                            }

                            rename(
                                $extractDirPath.'images/'.$picture,
                                Yii::getAlias('@web/app/web/img/materials/'.$picture)
                            );
                        }

                        $result['materials'][] = $material;
                    }
                } catch (Exception $e) {
                    $this->addError('file', $e->getMessage());

                    return null;
                }
            } finally {
                FileHelper::deleteDir($extractDirPath);
            }
        }

        return $result;
    }
}
