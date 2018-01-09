<?php

use yii\db\Migration;

/**
 * Handles the creation of table `roll`.
 */
class m180108_164940_create_roll_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('roll', [
            'id' => $this->string(13)->notNull(),
            'dices' => $this->string()->notNull(),
            'difficulty' => $this->integer()->notNull(),
            'rolls' => $this->string()->notNull(),
            'successes' => $this->integer()->notNull(),
            'character' => $this->string()->notNull(),
            'action' => $this->string()->notNull(),
            'success_modifier' => $this->integer()->notNull()->defaultValue(0),
            'ignore_critical_fail' => $this->boolean()->notNull()->defaultValue(false),
            'created_at' => $this->timestamp()->notNull(),
        ]);

        $this->addPrimaryKey('roll_pk', 'roll', 'id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('roll');
    }
}
