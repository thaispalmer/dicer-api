<?php

namespace api\modules\v1\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "roll".
 *
 * @property string $id
 * @property string $dices
 * @property string $rolls
 * @property string $character
 * @property string $action
 * @property integer $successes
 * @property integer $difficulty
 * @property integer $success_modifier
 * @property boolean $ignore_critical_fail
 * @property string $created_at
 */
class Roll extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'roll';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dices', 'character', 'action', 'difficulty'], 'required'],
            [['success_modifier', 'difficulty', 'successes'], 'integer'],
            [['success_modifier', 'successes'], 'default', 'value' => 0],
            [['ignore_critical_fail'], 'boolean'],
            [['ignore_critical_fail'], 'default', 'value' => false],
            [['created_at'], 'safe'],
            [['id'], 'string', 'max' => 13],
            [['id'], 'default', 'value' => function ($model, $attribute) {
                return uniqid();
            }],
            [['dices', 'rolls', 'character', 'action'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        $fields = parent::fields();

        $fields['created_at'] = function ($model) {
            /** @var static $model */
            return (empty($this->created_at)) ? null : date(DATE_W3C, strtotime($model->created_at));
        };

        return $fields;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dices' => 'Dices',
            'difficulty' => 'Difficulty',
            'rolls' => 'Rolls',
            'successes' => 'Successes',
            'character' => 'Character',
            'action' => 'Action',
            'success_modifier' => 'Success Modifier',
            'ignore_critical_fail' => 'Ignore Critical Fail',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        $dices = explode(',', $this->dices);
        $rolls = array();

        // Parse our dices
        foreach ($dices as $dice) {
            list($repeat, $faces) = explode('d', $dice);
            for ($i=0; $i < $repeat; $i++) {
                // Roll the dice!
                $rolls[] = rand(1,$faces);

                // If not ignoring critical fail, remove one success
                if ((end($rolls) == 1) && !$this->ignore_critical_fail) {
                    $this->successes--;
                }

                // Check for success
                if (end($rolls) >= $this->difficulty) {
                    $this->successes++;
                }
            }
        }

        // Apply success modifier
        $this->successes += $this->success_modifier;

        // Prevent negative successes
        if ($this->successes < 0) $this->successes = 0;

        // Concat rolls in a string and put on our model
        $this->rolls = implode($rolls, ',');

        return parent::beforeSave($insert);
    }
}