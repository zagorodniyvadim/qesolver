<?php namespace App\Database\Migrations;

class AddResponses extends \CodeIgniter\Database\Migration
{

    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'request_id' => [
                'type' => 'INT',
                'unsigned' => TRUE,
            ],
            'response' => [
                'type' => 'TEXT',
            ],
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->addForeignKey('request_id','requests','id');
        $this->forge->createTable('responses');
    }

    public function down()
    {
        $this->forge->dropTable('responses');
    }
}