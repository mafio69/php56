<?php

class Groups_rolesTableSeeder extends Seeder {

	public function run()
	{
		// Uncomment the below to wipe the table clean before populating
		// DB::table('groups_roles')->truncate();

		$root_group = DB::table('groups')
	                            ->select('id')
	                            ->where('name', 'Root')
	                            ->first()
	                            ->id;
	    
	    $admin_group = DB::table('groups')
	                            ->select('id')
	                            ->where('name', 'Administrator')
	                            ->first()
	                            ->id;

	    $admin_role = DB::table('roles')
	                            ->select('id')
	                            ->where('active', '0');
	                                               
		foreach ($admin_role as $key => $value) {
			$group_role = array(
				'id_role' => $value,
				'id_group' => $root_group
			);

			DB::table('groups_roles')->insert($group_role);

			$group_role = array(
				'id_role' => $value,
				'id_group' => $admin_group
			);
			
			DB::table('groups_roles')->insert($group_role);
		}                

	
	}

}
