<?php namespace App\Database\Migrations;

class AddRequests extends \CodeIgniter\Database\Migration
{

    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'a' => [
                'type' => 'INT',
            ],
            'b' => [
                'type' => 'INT',
            ],
            'c' => [
                'type' => 'INT',
            ],
            'token' => [
                'type' => 'VARCHAR',
                'constraint' => '40',
            ],
            'count' => [
                'type' => 'INT',
                'unsigned' => TRUE,
            ],
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->addKey('token');
        $this->forge->createTable('requests');
    }

    public function down()
    {
        $this->forge->dropTable('requests');
    }
}