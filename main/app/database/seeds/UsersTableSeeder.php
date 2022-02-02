<?php

class UsersTableSeeder extends Seeder {

	public function run()
	{
		// Uncomment the below to wipe the table clean before populating
		// DB::table('users')->truncate();

		$users = array(
			'login' => 'test',
			'name' => 'Test user',
			'password' => Hash::make('test'),
			'typ' => '1',			
			'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
		);

		// Uncomment the below to run the seeder
		DB::table('users')->insert($users);
	}

}
