<?php

use Phinx\Migration\AbstractMigration;

class SessionComments extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $sessionDescription = $this->table(
            'session_description',
            [
                'id' => false,
                'primary_key' =>
                [
                    'session_id'
                ]
            ]
        );

        $sessionDescription->addColumn(
            'session_id',
            'integer',
            [
                'limit' => 11,
                'signed' => false
            ]
        )->addColumn(
            'text',
            'string',
            [
                'limit' => 2000
            ]
        )->addColumn(
            'created',
            'datetime',
            [
                'null' => true
            ]
        )->addColumn(
            'modified',
            'datetime',
            [
                'null' => true
            ]
        );

        $sessionDescription->addForeignKey(
            'session_id',
            'session',
            ['id'],
            [
                'update' => 'CASCADE',
                'delete' => 'CASCADE'
            ]
        );

        $sessionDescription->addIndex('created');
        $sessionDescription->addIndex('modified');
        $sessionDescription->save();
    }
}
