<?php

class RolesTableSeeder extends Seeder {

	public function run()
	{
		// Uncomment the below to wipe the table clean before populating
		//DB::table('roles')->truncate();

		$roles = array(
			array(
				'name' => 'wyświetlanie firm',
				'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ),
			array(
				'name' => 'zarządzanie firmami',
				'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
				),
			array(
				'name' => 'wyświetlanie oddziałów',
				'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
				),
			array(
				'name' => 'zarządzanie firmami',
				'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
				),
			array(
				'name' => 'wyświetlanie marek',
				'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
				),
			array(
				'name' => 'zarządzanie markami',
				'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
				)
		);

		// Uncomment the below to run the seeder
		DB::table('roles')->insert($roles);


			      


		$groups = array(
			array(
				'name' => 'Root', 
				'opis' => 'root aplikacji',
				'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ),
			array(
				'name' => 'Administrator', 
				'opis' => 'administator aplikacji',
				'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s') 
			)
		);

		// Uncomment the below to run the seeder
		DB::table('groups')->insert($groups);


	}
		

}
