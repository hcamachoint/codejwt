<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Blogs extends Migration
{
	public function up()
	{
		$this->forge->addField([
        'id'          => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => TRUE,
                'auto_increment' => TRUE
        ],
				'title'       => [
                'type'           => 'VARCHAR',
                'constraint'     => '50',
        ],
				'description'       => [
                'type'           => 'TEXT',
        ],
    ]);
    $this->forge->addKey('id', TRUE);
    $this->forge->createTable('blogs');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('blogs');
	}
}
