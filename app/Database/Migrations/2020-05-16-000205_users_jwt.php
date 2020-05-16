<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UserJwt extends Migration
{
	public function up()
	{
		$this->db->disableForeignKeyChecks();
		$this->forge->addField([
            'id'          => [
                    'type'           => 'INT',
                    'constraint'     => 5,
                    'unsigned'       => true,
                    'auto_increment' => true
            ],
						'token'       => [
                    'type'           => 'VARCHAR',
                    'constraint'     => '300',
										'unique'         => true,
										'null'					 =>	false
            ],
						'user'       => [
										'type'           => 'INT',
						],
						'created_at'       => [
                    'type'           => 'datetime',
            ],
						'updated_at'       => [
                    'type'           => 'datetime',
            ],
    ]);
    $this->forge->addKey('id', TRUE);
		$this->forge->addForeignKey('user','users','id','CASCADE','CASCADE');
    $this->forge->createTable('users_jwt');
		$this->db->enableForeignKeyChecks();
	}

	public function down()
	{
		$this->forge->dropTable('users_jwt');
	}
}
